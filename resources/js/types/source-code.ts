export type SourceCodeLanguage =
    | 'javascript'
    | 'json'
    | 'php'
    | 'svelte'
    | 'typescript';

export type SourceCodeFile = {
    id: string;
    label: string;
    language: SourceCodeLanguage;
    code: string;
};

export type SourceCodeResponse = {
    sourceSet: string;
    title: string;
    files: SourceCodeFile[];
};
