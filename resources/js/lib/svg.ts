export function replaceWithSvgMarkup(
    element: HTMLElement,
    markup: string,
): void {
    const parsedDocument = new DOMParser().parseFromString(markup, 'text/html');
    const parsedSvg = parsedDocument.querySelector('svg');

    if (!parsedSvg) {
        throw new Error('SVG markup does not contain an SVG element.');
    }

    element.replaceChildren(document.importNode(parsedSvg, true));
}
