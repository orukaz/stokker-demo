import type { ThemedToken } from 'shiki/types';
import type { SourceCodeLanguage } from '@/types/source-code';

let highlighterPromise:
    | ReturnType<typeof createSourceCodeHighlighter>
    | undefined;

async function createSourceCodeHighlighter() {
    const [
        { createHighlighterCore },
        { createJavaScriptRegexEngine },
        { default: githubDarkDefault },
        { default: javascript },
        { default: json },
        { default: php },
        { default: svelte },
    ] = await Promise.all([
        import('shiki/core'),
        import('shiki/engine/javascript'),
        import('@shikijs/themes/github-dark-default'),
        import('@shikijs/langs/javascript'),
        import('@shikijs/langs/json'),
        import('@shikijs/langs/php'),
        import('@shikijs/langs/svelte'),
    ]);
    const languages = [...svelte, ...php, ...json, ...javascript].filter(
        (language, index, allLanguages) =>
            allLanguages.findIndex(({ name }) => name === language.name) ===
            index,
    );

    return createHighlighterCore({
        themes: [githubDarkDefault],
        langs: languages,
        engine: createJavaScriptRegexEngine(),
    });
}

function getHighlighter(): ReturnType<typeof createSourceCodeHighlighter> {
    highlighterPromise ??= createSourceCodeHighlighter();

    return highlighterPromise;
}

export async function highlightCode(
    code: string,
    language: SourceCodeLanguage,
): Promise<ThemedToken[][]> {
    const highlighter = await getHighlighter();

    return highlighter.codeToTokensBase(code, {
        lang: language,
        theme: 'github-dark-default',
        tokenizeMaxLineLength: 2_000,
        tokenizeTimeLimit: 3_000,
    });
}
