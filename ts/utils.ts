export const d = document;
export const elem = d.createElement.bind(d) as typeof d.createElement;
export const textNode = d.createTextNode.bind(d) as typeof d.createTextNode;
export const id = d.getElementById.bind(d) as typeof d.getElementById;
export const qs = d.querySelector.bind(d) as typeof d.querySelector;
export const qsa = d.querySelectorAll.bind(d) as typeof d.querySelectorAll;

export function addClass(selector: string, ...classes: Array<string>): void {
	qsa(selector).forEach((el) => {
		el.classList.add(...classes);
	});
}

export function rmClass(selector: string, ...classes: Array<string>): void {
	qsa(selector).forEach((el) => {
		el.classList.remove(...classes);
	});
}

export function insertAfter(newNode: Element, referenceNode: Element): void {
	if (!referenceNode.parentNode) {
		throw 'element has no parentNode';
	}

	referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
}

export function wrap(el: Element, wrapper: Element): void {
	if (!el.parentNode) {
		throw 'element has no parentNode';
	}

	el.parentNode.insertBefore(wrapper, el);
	wrapper.appendChild(el);
}

export function elWithClass(el: string, ...classes: Array<string>): HTMLElement {
	const ele = elem(el);
	ele.classList.add(...classes);
	return ele;
}

// Wrap wrapper around nodes
// Just pass a collection of nodes, and a wrapper element
export function wrapAll(nodes: NodeList, wrapper: HTMLElement): void {
	if (nodes.length <= 0) {
		return;
	}

	// Cache the current parent and previous sibling of the first node.
	const parent = nodes[0].parentNode;

	if (!parent) {
		throw 'Element has no parent';
	}

	const previousSibling = nodes[0].previousSibling;

	// Place each node in wrapper.
	//  - If nodes is an array, we must increment the index we grab from
	//    after each loop.
	//  - If nodes is a NodeList, each node is automatically removed from
	//    the NodeList when it is removed from its parent with appendChild.
	for (let i = 0; nodes.length - i; wrapper.firstChild === nodes[0] && i++) {
		wrapper.appendChild(nodes[i]);
	}

	// Place the wrapper just after the cached previousSibling,
	// or if that is null, just before the first child.
	const nextSibling = previousSibling ? previousSibling.nextSibling : parent.firstChild;
	parent.insertBefore(wrapper, nextSibling);

	//return wrapper;
}
