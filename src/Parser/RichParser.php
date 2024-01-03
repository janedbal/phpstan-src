<?php declare(strict_types = 1);

namespace PHPStan\Parser;

use PhpParser\ErrorHandler\Collecting;
use PhpParser\Lexer;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PHPStan\DependencyInjection\Container;
use PHPStan\File\FileReader;
use PHPStan\ShouldNotHappenException;
use function array_filter;
use function is_string;
use function strpos;
use function substr;
use function substr_count;
use const ARRAY_FILTER_USE_KEY;
use const T_COMMENT;
use const T_DOC_COMMENT;

class RichParser implements Parser
{

	public const VISITOR_SERVICE_TAG = 'phpstan.parser.richParserNodeVisitor';

	public function __construct(
		private \PhpParser\Parser $parser,
		private Lexer $lexer,
		private NameResolver $nameResolver,
		private Container $container,
		private bool $enableIgnoreErrorsWithinPhpDocs,
	)
	{
	}

	/**
	 * @param string $file path to a file to parse
	 * @return Node\Stmt[]
	 */
	public function parseFile(string $file): array
	{
		try {
			return $this->parseString(FileReader::read($file));
		} catch (ParserErrorsException $e) {
			throw new ParserErrorsException($e->getErrors(), $file);
		}
	}

	/**
	 * @return Node\Stmt[]
	 */
	public function parseString(string $sourceCode): array
	{
		$errorHandler = new Collecting();
		$nodes = $this->parser->parse($sourceCode, $errorHandler);
		$tokens = $this->lexer->getTokens();
		if ($errorHandler->hasErrors()) {
			throw new ParserErrorsException($errorHandler->getErrors(), null);
		}
		if ($nodes === null) {
			throw new ShouldNotHappenException();
		}

		$nodeTraverser = new NodeTraverser();
		$nodeTraverser->addVisitor($this->nameResolver);

		$traitCollectingVisitor = new TraitCollectingVisitor();
		$nodeTraverser->addVisitor($traitCollectingVisitor);

		foreach ($this->container->getServicesByTag(self::VISITOR_SERVICE_TAG) as $visitor) {
			$nodeTraverser->addVisitor($visitor);
		}

		/** @var array<Node\Stmt> */
		$nodes = $nodeTraverser->traverse($nodes);
		$linesToIgnore = $this->getLinesToIgnore($tokens);
		if (isset($nodes[0])) {
			$nodes[0]->setAttribute('linesToIgnore', $linesToIgnore);
		}

		foreach ($traitCollectingVisitor->traits as $trait) {
			$trait->setAttribute('linesToIgnore', array_filter($linesToIgnore, static fn (int $line): bool => $line >= $trait->getStartLine() && $line <= $trait->getEndLine(), ARRAY_FILTER_USE_KEY));
		}

		return $nodes;
	}

	/**
	 * @param mixed[] $tokens
	 * @return array<int, list<string>|null>
	 */
	private function getLinesToIgnore(array $tokens): array
	{
		$lines = [];
		foreach ($tokens as $token) {
			if (is_string($token)) {
				continue;
			}

			$type = $token[0];
			if ($type !== T_COMMENT && $type !== T_DOC_COMMENT) {
				continue;
			}

			$text = $token[1];
			$line = $token[2];

			if ($this->enableIgnoreErrorsWithinPhpDocs) {
				$lines = $lines +
					$this->getLinesToIgnoreForTokenByIgnoreComment($text, $line, '@phpstan-ignore-next-line', true) +
					$this->getLinesToIgnoreForTokenByIgnoreComment($text, $line, '@phpstan-ignore-line');

			} else {
				if (strpos($text, '@phpstan-ignore-next-line') !== false) {
					$line++;
				} elseif (strpos($text, '@phpstan-ignore-line') === false) {
					continue;
				}

				$line += substr_count($token[1], "\n");
				$lines[$line] = null;
			}
		}

		return $lines;
	}

	/**
	 * @return array<int, null>
	 */
	private function getLinesToIgnoreForTokenByIgnoreComment(
		string $tokenText,
		int $tokenLine,
		string $ignoreComment,
		bool $ignoreNextLine = false,
	): array
	{
		$lines = [];
		$positionsOfIgnoreComment = [];
		$offset = 0;

		while (($pos = strpos($tokenText, $ignoreComment, $offset)) !== false) {
			$positionsOfIgnoreComment[] = $pos;
			$offset = $pos + 1;
		}

		foreach ($positionsOfIgnoreComment as $pos) {
			$line = $tokenLine + substr_count(substr($tokenText, 0, $pos), "\n") + ($ignoreNextLine ? 1 : 0);
			$lines[$line] = null;
		}

		return $lines;
	}

}
