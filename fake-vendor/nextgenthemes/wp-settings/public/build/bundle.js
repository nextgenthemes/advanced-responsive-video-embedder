
(function(l, r) { if (!l || l.getElementById('livereloadscript')) return; r = l.createElement('script'); r.async = 1; r.src = '//' + (self.location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1'; r.id = 'livereloadscript'; l.getElementsByTagName('head')[0].appendChild(r) })(self.document);
var app = (function () {
    'use strict';

    function noop() { }
    function add_location(element, file, line, column, char) {
        element.__svelte_meta = {
            loc: { file, line, column, char }
        };
    }
    function run(fn) {
        return fn();
    }
    function blank_object() {
        return Object.create(null);
    }
    function run_all(fns) {
        fns.forEach(run);
    }
    function is_function(thing) {
        return typeof thing === 'function';
    }
    function safe_not_equal(a, b) {
        return a != a ? b == b : a !== b || ((a && typeof a === 'object') || typeof a === 'function');
    }
    function is_empty(obj) {
        return Object.keys(obj).length === 0;
    }
    function validate_store(store, name) {
        if (store != null && typeof store.subscribe !== 'function') {
            throw new Error(`'${name}' is not a store with a 'subscribe' method`);
        }
    }
    function subscribe(store, ...callbacks) {
        if (store == null) {
            return noop;
        }
        const unsub = store.subscribe(...callbacks);
        return unsub.unsubscribe ? () => unsub.unsubscribe() : unsub;
    }
    function component_subscribe(component, store, callback) {
        component.$$.on_destroy.push(subscribe(store, callback));
    }
    function set_store_value(store, ret, value) {
        store.set(value);
        return ret;
    }
    function append(target, node) {
        target.appendChild(node);
    }
    function insert(target, node, anchor) {
        target.insertBefore(node, anchor || null);
    }
    function detach(node) {
        if (node.parentNode) {
            node.parentNode.removeChild(node);
        }
    }
    function destroy_each(iterations, detaching) {
        for (let i = 0; i < iterations.length; i += 1) {
            if (iterations[i])
                iterations[i].d(detaching);
        }
    }
    function element(name) {
        return document.createElement(name);
    }
    function text(data) {
        return document.createTextNode(data);
    }
    function space() {
        return text(' ');
    }
    function empty() {
        return text('');
    }
    function listen(node, event, handler, options) {
        node.addEventListener(event, handler, options);
        return () => node.removeEventListener(event, handler, options);
    }
    function attr(node, attribute, value) {
        if (value == null)
            node.removeAttribute(attribute);
        else if (node.getAttribute(attribute) !== value)
            node.setAttribute(attribute, value);
    }
    function to_number(value) {
        return value === '' ? null : +value;
    }
    function children(element) {
        return Array.from(element.childNodes);
    }
    function set_input_value(input, value) {
        input.value = value == null ? '' : value;
    }
    function select_option(select, value, mounting) {
        for (let i = 0; i < select.options.length; i += 1) {
            const option = select.options[i];
            if (option.__value === value) {
                option.selected = true;
                return;
            }
        }
        if (!mounting || value !== undefined) {
            select.selectedIndex = -1; // no option should be selected
        }
    }
    function select_value(select) {
        const selected_option = select.querySelector(':checked');
        return selected_option && selected_option.__value;
    }
    function toggle_class(element, name, toggle) {
        element.classList[toggle ? 'add' : 'remove'](name);
    }
    function custom_event(type, detail, { bubbles = false, cancelable = false } = {}) {
        const e = document.createEvent('CustomEvent');
        e.initCustomEvent(type, bubbles, cancelable, detail);
        return e;
    }

    let current_component;
    function set_current_component(component) {
        current_component = component;
    }
    function get_current_component() {
        if (!current_component)
            throw new Error('Function called outside component initialization');
        return current_component;
    }
    /**
     * The `onMount` function schedules a callback to run as soon as the component has been mounted to the DOM.
     * It must be called during the component's initialisation (but doesn't need to live *inside* the component;
     * it can be called from an external module).
     *
     * `onMount` does not run inside a [server-side component](/docs#run-time-server-side-component-api).
     *
     * https://svelte.dev/docs#run-time-svelte-onmount
     */
    function onMount(fn) {
        get_current_component().$$.on_mount.push(fn);
    }

    const dirty_components = [];
    const binding_callbacks = [];
    let render_callbacks = [];
    const flush_callbacks = [];
    const resolved_promise = /* @__PURE__ */ Promise.resolve();
    let update_scheduled = false;
    function schedule_update() {
        if (!update_scheduled) {
            update_scheduled = true;
            resolved_promise.then(flush);
        }
    }
    function add_render_callback(fn) {
        render_callbacks.push(fn);
    }
    // flush() calls callbacks in this order:
    // 1. All beforeUpdate callbacks, in order: parents before children
    // 2. All bind:this callbacks, in reverse order: children before parents.
    // 3. All afterUpdate callbacks, in order: parents before children. EXCEPT
    //    for afterUpdates called during the initial onMount, which are called in
    //    reverse order: children before parents.
    // Since callbacks might update component values, which could trigger another
    // call to flush(), the following steps guard against this:
    // 1. During beforeUpdate, any updated components will be added to the
    //    dirty_components array and will cause a reentrant call to flush(). Because
    //    the flush index is kept outside the function, the reentrant call will pick
    //    up where the earlier call left off and go through all dirty components. The
    //    current_component value is saved and restored so that the reentrant call will
    //    not interfere with the "parent" flush() call.
    // 2. bind:this callbacks cannot trigger new flush() calls.
    // 3. During afterUpdate, any updated components will NOT have their afterUpdate
    //    callback called a second time; the seen_callbacks set, outside the flush()
    //    function, guarantees this behavior.
    const seen_callbacks = new Set();
    let flushidx = 0; // Do *not* move this inside the flush() function
    function flush() {
        // Do not reenter flush while dirty components are updated, as this can
        // result in an infinite loop. Instead, let the inner flush handle it.
        // Reentrancy is ok afterwards for bindings etc.
        if (flushidx !== 0) {
            return;
        }
        const saved_component = current_component;
        do {
            // first, call beforeUpdate functions
            // and update components
            try {
                while (flushidx < dirty_components.length) {
                    const component = dirty_components[flushidx];
                    flushidx++;
                    set_current_component(component);
                    update(component.$$);
                }
            }
            catch (e) {
                // reset dirty state to not end up in a deadlocked state and then rethrow
                dirty_components.length = 0;
                flushidx = 0;
                throw e;
            }
            set_current_component(null);
            dirty_components.length = 0;
            flushidx = 0;
            while (binding_callbacks.length)
                binding_callbacks.pop()();
            // then, once components are updated, call
            // afterUpdate functions. This may cause
            // subsequent updates...
            for (let i = 0; i < render_callbacks.length; i += 1) {
                const callback = render_callbacks[i];
                if (!seen_callbacks.has(callback)) {
                    // ...so guard against infinite loops
                    seen_callbacks.add(callback);
                    callback();
                }
            }
            render_callbacks.length = 0;
        } while (dirty_components.length);
        while (flush_callbacks.length) {
            flush_callbacks.pop()();
        }
        update_scheduled = false;
        seen_callbacks.clear();
        set_current_component(saved_component);
    }
    function update($$) {
        if ($$.fragment !== null) {
            $$.update();
            run_all($$.before_update);
            const dirty = $$.dirty;
            $$.dirty = [-1];
            $$.fragment && $$.fragment.p($$.ctx, dirty);
            $$.after_update.forEach(add_render_callback);
        }
    }
    /**
     * Useful for example to execute remaining `afterUpdate` callbacks before executing `destroy`.
     */
    function flush_render_callbacks(fns) {
        const filtered = [];
        const targets = [];
        render_callbacks.forEach((c) => fns.indexOf(c) === -1 ? filtered.push(c) : targets.push(c));
        targets.forEach((c) => c());
        render_callbacks = filtered;
    }
    const outroing = new Set();
    let outros;
    function group_outros() {
        outros = {
            r: 0,
            c: [],
            p: outros // parent group
        };
    }
    function check_outros() {
        if (!outros.r) {
            run_all(outros.c);
        }
        outros = outros.p;
    }
    function transition_in(block, local) {
        if (block && block.i) {
            outroing.delete(block);
            block.i(local);
        }
    }
    function transition_out(block, local, detach, callback) {
        if (block && block.o) {
            if (outroing.has(block))
                return;
            outroing.add(block);
            outros.c.push(() => {
                outroing.delete(block);
                if (callback) {
                    if (detach)
                        block.d(1);
                    callback();
                }
            });
            block.o(local);
        }
        else if (callback) {
            callback();
        }
    }

    const globals = (typeof window !== 'undefined'
        ? window
        : typeof globalThis !== 'undefined'
            ? globalThis
            : global);
    function create_component(block) {
        block && block.c();
    }
    function mount_component(component, target, anchor, customElement) {
        const { fragment, after_update } = component.$$;
        fragment && fragment.m(target, anchor);
        if (!customElement) {
            // onMount happens before the initial afterUpdate
            add_render_callback(() => {
                const new_on_destroy = component.$$.on_mount.map(run).filter(is_function);
                // if the component was destroyed immediately
                // it will update the `$$.on_destroy` reference to `null`.
                // the destructured on_destroy may still reference to the old array
                if (component.$$.on_destroy) {
                    component.$$.on_destroy.push(...new_on_destroy);
                }
                else {
                    // Edge case - component was destroyed immediately,
                    // most likely as a result of a binding initialising
                    run_all(new_on_destroy);
                }
                component.$$.on_mount = [];
            });
        }
        after_update.forEach(add_render_callback);
    }
    function destroy_component(component, detaching) {
        const $$ = component.$$;
        if ($$.fragment !== null) {
            flush_render_callbacks($$.after_update);
            run_all($$.on_destroy);
            $$.fragment && $$.fragment.d(detaching);
            // TODO null out other refs, including component.$$ (but need to
            // preserve final state?)
            $$.on_destroy = $$.fragment = null;
            $$.ctx = [];
        }
    }
    function make_dirty(component, i) {
        if (component.$$.dirty[0] === -1) {
            dirty_components.push(component);
            schedule_update();
            component.$$.dirty.fill(0);
        }
        component.$$.dirty[(i / 31) | 0] |= (1 << (i % 31));
    }
    function init(component, options, instance, create_fragment, not_equal, props, append_styles, dirty = [-1]) {
        const parent_component = current_component;
        set_current_component(component);
        const $$ = component.$$ = {
            fragment: null,
            ctx: [],
            // state
            props,
            update: noop,
            not_equal,
            bound: blank_object(),
            // lifecycle
            on_mount: [],
            on_destroy: [],
            on_disconnect: [],
            before_update: [],
            after_update: [],
            context: new Map(options.context || (parent_component ? parent_component.$$.context : [])),
            // everything else
            callbacks: blank_object(),
            dirty,
            skip_bound: false,
            root: options.target || parent_component.$$.root
        };
        append_styles && append_styles($$.root);
        let ready = false;
        $$.ctx = instance
            ? instance(component, options.props || {}, (i, ret, ...rest) => {
                const value = rest.length ? rest[0] : ret;
                if ($$.ctx && not_equal($$.ctx[i], $$.ctx[i] = value)) {
                    if (!$$.skip_bound && $$.bound[i])
                        $$.bound[i](value);
                    if (ready)
                        make_dirty(component, i);
                }
                return ret;
            })
            : [];
        $$.update();
        ready = true;
        run_all($$.before_update);
        // `false` as a special case of no DOM component
        $$.fragment = create_fragment ? create_fragment($$.ctx) : false;
        if (options.target) {
            if (options.hydrate) {
                const nodes = children(options.target);
                // eslint-disable-next-line @typescript-eslint/no-non-null-assertion
                $$.fragment && $$.fragment.l(nodes);
                nodes.forEach(detach);
            }
            else {
                // eslint-disable-next-line @typescript-eslint/no-non-null-assertion
                $$.fragment && $$.fragment.c();
            }
            if (options.intro)
                transition_in(component.$$.fragment);
            mount_component(component, options.target, options.anchor, options.customElement);
            flush();
        }
        set_current_component(parent_component);
    }
    /**
     * Base class for Svelte components. Used when dev=false.
     */
    class SvelteComponent {
        $destroy() {
            destroy_component(this, 1);
            this.$destroy = noop;
        }
        $on(type, callback) {
            if (!is_function(callback)) {
                return noop;
            }
            const callbacks = (this.$$.callbacks[type] || (this.$$.callbacks[type] = []));
            callbacks.push(callback);
            return () => {
                const index = callbacks.indexOf(callback);
                if (index !== -1)
                    callbacks.splice(index, 1);
            };
        }
        $set($$props) {
            if (this.$$set && !is_empty($$props)) {
                this.$$.skip_bound = true;
                this.$$set($$props);
                this.$$.skip_bound = false;
            }
        }
    }

    function dispatch_dev(type, detail) {
        document.dispatchEvent(custom_event(type, Object.assign({ version: '3.58.0' }, detail), { bubbles: true }));
    }
    function append_dev(target, node) {
        dispatch_dev('SvelteDOMInsert', { target, node });
        append(target, node);
    }
    function insert_dev(target, node, anchor) {
        dispatch_dev('SvelteDOMInsert', { target, node, anchor });
        insert(target, node, anchor);
    }
    function detach_dev(node) {
        dispatch_dev('SvelteDOMRemove', { node });
        detach(node);
    }
    function listen_dev(node, event, handler, options, has_prevent_default, has_stop_propagation, has_stop_immediate_propagation) {
        const modifiers = options === true ? ['capture'] : options ? Array.from(Object.keys(options)) : [];
        if (has_prevent_default)
            modifiers.push('preventDefault');
        if (has_stop_propagation)
            modifiers.push('stopPropagation');
        if (has_stop_immediate_propagation)
            modifiers.push('stopImmediatePropagation');
        dispatch_dev('SvelteDOMAddEventListener', { node, event, handler, modifiers });
        const dispose = listen(node, event, handler, options);
        return () => {
            dispatch_dev('SvelteDOMRemoveEventListener', { node, event, handler, modifiers });
            dispose();
        };
    }
    function attr_dev(node, attribute, value) {
        attr(node, attribute, value);
        if (value == null)
            dispatch_dev('SvelteDOMRemoveAttribute', { node, attribute });
        else
            dispatch_dev('SvelteDOMSetAttribute', { node, attribute, value });
    }
    function prop_dev(node, property, value) {
        node[property] = value;
        dispatch_dev('SvelteDOMSetProperty', { node, property, value });
    }
    function set_data_dev(text, data) {
        data = '' + data;
        if (text.data === data)
            return;
        dispatch_dev('SvelteDOMSetData', { node: text, data });
        text.data = data;
    }
    function validate_each_argument(arg) {
        if (typeof arg !== 'string' && !(arg && typeof arg === 'object' && 'length' in arg)) {
            let msg = '{#each} only iterates over array-like objects.';
            if (typeof Symbol === 'function' && arg && Symbol.iterator in arg) {
                msg += ' You can use a spread to convert this iterable into an array.';
            }
            throw new Error(msg);
        }
    }
    function validate_slots(name, slot, keys) {
        for (const slot_key of Object.keys(slot)) {
            if (!~keys.indexOf(slot_key)) {
                console.warn(`<${name}> received an unexpected slot "${slot_key}".`);
            }
        }
    }
    /**
     * Base class for Svelte components with some minor dev-enhancements. Used when dev=true.
     */
    class SvelteComponentDev extends SvelteComponent {
        constructor(options) {
            if (!options || (!options.target && !options.$$inline)) {
                throw new Error("'target' is a required option");
            }
            super();
        }
        $destroy() {
            super.$destroy();
            this.$destroy = () => {
                console.warn('Component was already destroyed'); // eslint-disable-line no-console
            };
        }
        $capture_state() { }
        $inject_state() { }
    }

    const subscriber_queue = [];
    /**
     * Create a `Writable` store that allows both updating and reading by subscription.
     * @param {*=}value initial value
     * @param {StartStopNotifier=}start start and stop notifications for subscriptions
     */
    function writable(value, start = noop) {
        let stop;
        const subscribers = new Set();
        function set(new_value) {
            if (safe_not_equal(value, new_value)) {
                value = new_value;
                if (stop) { // store is ready
                    const run_queue = !subscriber_queue.length;
                    for (const subscriber of subscribers) {
                        subscriber[1]();
                        subscriber_queue.push(subscriber, value);
                    }
                    if (run_queue) {
                        for (let i = 0; i < subscriber_queue.length; i += 2) {
                            subscriber_queue[i][0](subscriber_queue[i + 1]);
                        }
                        subscriber_queue.length = 0;
                    }
                }
            }
        }
        function update(fn) {
            set(fn(value));
        }
        function subscribe(run, invalidate = noop) {
            const subscriber = [run, invalidate];
            subscribers.add(subscriber);
            if (subscribers.size === 1) {
                stop = start(set) || noop;
            }
            run(value);
            return () => {
                subscribers.delete(subscriber);
                if (subscribers.size === 0 && stop) {
                    stop();
                    stop = null;
                }
            };
        }
        return { set, update, subscribe };
    }

    const url = new URL(window.location.href);
    const pageQueryVal = url.searchParams.get('page');
    if (!pageQueryVal) {
        throw 'Need page url arg';
    }
    const data = window[pageQueryVal];
    const settings = data.settings;
    const sections = data.sections;
    const options = writable(data.options);
    const nonce = data.nonce;
    const restURL = data.rest_url;
    data.premium_sections;
    const message = writable('');

    /* src/Labeltext.svelte generated by Svelte v3.58.0 */

    const { console: console_1$1 } = globals;
    const file$2 = "src/Labeltext.svelte";

    // (12:1) {#if true}
    function create_if_block$2(ctx) {
    	let span0;
    	let a;
    	let t1;
    	let span1;

    	const block = {
    		c: function create() {
    			span0 = element("span");
    			span0.textContent = "(";
    			a = element("a");
    			t1 = text(/*sectionLabel*/ ctx[3]);
    			span1 = element("span");
    			span1.textContent = ")";
    			attr_dev(span0, "class", "svelte-glj1of");
    			add_location(span0, file$2, 12, 3, 448);
    			attr_dev(a, "href", /*premiumUrl*/ ctx[2]);
    			add_location(a, file$2, 12, 17, 462);
    			attr_dev(span1, "class", "svelte-glj1of");
    			add_location(span1, file$2, 12, 56, 501);
    		},
    		m: function mount(target, anchor) {
    			insert_dev(target, span0, anchor);
    			insert_dev(target, a, anchor);
    			append_dev(a, t1);
    			insert_dev(target, span1, anchor);
    		},
    		p: noop,
    		d: function destroy(detaching) {
    			if (detaching) detach_dev(span0);
    			if (detaching) detach_dev(a);
    			if (detaching) detach_dev(span1);
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_if_block$2.name,
    		type: "if",
    		source: "(12:1) {#if true}",
    		ctx
    	});

    	return block;
    }

    function create_fragment$2(ctx) {
    	let span;
    	let t0;
    	let t1;
    	let if_block = create_if_block$2(ctx);

    	const block = {
    		c: function create() {
    			span = element("span");
    			t0 = text(/*label*/ ctx[1]);
    			t1 = space();
    			if (if_block) if_block.c();
    			attr_dev(span, "class", "ngt-label-text ngt-label-text--" + /*type*/ ctx[0] + " svelte-glj1of");
    			add_location(span, file$2, 9, 0, 371);
    		},
    		l: function claim(nodes) {
    			throw new Error("options.hydrate only works if the component was compiled with the `hydratable: true` option");
    		},
    		m: function mount(target, anchor) {
    			insert_dev(target, span, anchor);
    			append_dev(span, t0);
    			append_dev(span, t1);
    			if (if_block) if_block.m(span, null);
    		},
    		p: function update(ctx, [dirty]) {
    			if_block.p(ctx, dirty);
    		},
    		i: noop,
    		o: noop,
    		d: function destroy(detaching) {
    			if (detaching) detach_dev(span);
    			if (if_block) if_block.d();
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_fragment$2.name,
    		type: "component",
    		source: "",
    		ctx
    	});

    	return block;
    }

    function instance$2($$self, $$props, $$invalidate) {
    	let { $$slots: slots = {}, $$scope } = $$props;
    	validate_slots('Labeltext', slots, []);
    	const { log } = console;
    	let { optionKey } = $$props;
    	const type = settings[optionKey].type;
    	const label = settings[optionKey].label;
    	const premiumUrl = 'https://nextgenthemes.com/plugins/arve-' + settings[optionKey].tag;
    	const sectionLabel = sections[settings[optionKey].tag];

    	$$self.$$.on_mount.push(function () {
    		if (optionKey === undefined && !('optionKey' in $$props || $$self.$$.bound[$$self.$$.props['optionKey']])) {
    			console_1$1.warn("<Labeltext> was created without expected prop 'optionKey'");
    		}
    	});

    	const writable_props = ['optionKey'];

    	Object.keys($$props).forEach(key => {
    		if (!~writable_props.indexOf(key) && key.slice(0, 2) !== '$$' && key !== 'slot') console_1$1.warn(`<Labeltext> was created with unknown prop '${key}'`);
    	});

    	$$self.$$set = $$props => {
    		if ('optionKey' in $$props) $$invalidate(4, optionKey = $$props.optionKey);
    	};

    	$$self.$capture_state = () => ({
    		options,
    		settings,
    		sections,
    		restURL,
    		nonce,
    		log,
    		optionKey,
    		type,
    		label,
    		premiumUrl,
    		sectionLabel
    	});

    	$$self.$inject_state = $$props => {
    		if ('optionKey' in $$props) $$invalidate(4, optionKey = $$props.optionKey);
    	};

    	if ($$props && "$$inject" in $$props) {
    		$$self.$inject_state($$props.$$inject);
    	}

    	return [type, label, premiumUrl, sectionLabel, optionKey];
    }

    class Labeltext extends SvelteComponentDev {
    	constructor(options) {
    		super(options);
    		init(this, options, instance$2, create_fragment$2, safe_not_equal, { optionKey: 4 });

    		dispatch_dev("SvelteRegisterComponent", {
    			component: this,
    			tagName: "Labeltext",
    			options,
    			id: create_fragment$2.name
    		});
    	}

    	get optionKey() {
    		throw new Error("<Labeltext>: Props cannot be read directly from the component instance unless compiling with 'accessors: true' or '<svelte:options accessors/>'");
    	}

    	set optionKey(value) {
    		throw new Error("<Labeltext>: Props cannot be set directly on the component instance unless compiling with 'accessors: true' or '<svelte:options accessors/>'");
    	}
    }

    /* src/Setting.svelte generated by Svelte v3.58.0 */

    const { Object: Object_1$1, console: console_1 } = globals;
    const file$1 = "src/Setting.svelte";

    function get_each_context$1(ctx, list, i) {
    	const child_ctx = ctx.slice();
    	child_ctx[22] = list[i][0];
    	child_ctx[23] = list[i][1];
    	return child_ctx;
    }

    // (96:2) {:else}
    function create_else_block(ctx) {
    	let h3;

    	const block = {
    		c: function create() {
    			h3 = element("h3");
    			h3.textContent = `Error: ${/*type*/ ctx[3]} not implemented`;
    			add_location(h3, file$1, 97, 3, 3056);
    		},
    		m: function mount(target, anchor) {
    			insert_dev(target, h3, anchor);
    		},
    		p: noop,
    		i: noop,
    		o: noop,
    		d: function destroy(detaching) {
    			if (detaching) detach_dev(h3);
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_else_block.name,
    		type: "else",
    		source: "(96:2) {:else}",
    		ctx
    	});

    	return block;
    }

    // (89:31) 
    function create_if_block_4(ctx) {
    	let label_1;
    	let labeltext;
    	let t;
    	let input;
    	let current;
    	let mounted;
    	let dispose;

    	labeltext = new Labeltext({
    			props: { optionKey: /*optionKey*/ ctx[0] },
    			$$inline: true
    		});

    	const block = {
    		c: function create() {
    			label_1 = element("label");
    			create_component(labeltext.$$.fragment);
    			t = space();
    			input = element("input");
    			attr_dev(input, "type", "number");
    			add_location(input, file$1, 92, 4, 2927);
    			add_location(label_1, file$1, 90, 3, 2885);
    		},
    		m: function mount(target, anchor) {
    			insert_dev(target, label_1, anchor);
    			mount_component(labeltext, label_1, null);
    			append_dev(label_1, t);
    			append_dev(label_1, input);
    			set_input_value(input, /*$options*/ ctx[1][/*optionKey*/ ctx[0]]);
    			current = true;

    			if (!mounted) {
    				dispose = [
    					listen_dev(input, "input", /*input_input_handler_1*/ ctx[12]),
    					listen_dev(input, "input", /*input_handler_1*/ ctx[13], false, false, false, false)
    				];

    				mounted = true;
    			}
    		},
    		p: function update(ctx, dirty) {
    			const labeltext_changes = {};
    			if (dirty & /*optionKey*/ 1) labeltext_changes.optionKey = /*optionKey*/ ctx[0];
    			labeltext.$set(labeltext_changes);

    			if (dirty & /*$options, optionKey, Object, settings*/ 3 && to_number(input.value) !== /*$options*/ ctx[1][/*optionKey*/ ctx[0]]) {
    				set_input_value(input, /*$options*/ ctx[1][/*optionKey*/ ctx[0]]);
    			}
    		},
    		i: function intro(local) {
    			if (current) return;
    			transition_in(labeltext.$$.fragment, local);
    			current = true;
    		},
    		o: function outro(local) {
    			transition_out(labeltext.$$.fragment, local);
    			current = false;
    		},
    		d: function destroy(detaching) {
    			if (detaching) detach_dev(label_1);
    			destroy_component(labeltext);
    			mounted = false;
    			run_all(dispose);
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_if_block_4.name,
    		type: "if",
    		source: "(89:31) ",
    		ctx
    	});

    	return block;
    }

    // (76:30) 
    function create_if_block_3(ctx) {
    	let label_1;
    	let labeltext;
    	let t;
    	let select;
    	let current;
    	let mounted;
    	let dispose;

    	labeltext = new Labeltext({
    			props: { optionKey: /*optionKey*/ ctx[0] },
    			$$inline: true
    		});

    	let each_value = Object.entries(settings[/*optionKey*/ ctx[0]].options);
    	validate_each_argument(each_value);
    	let each_blocks = [];

    	for (let i = 0; i < each_value.length; i += 1) {
    		each_blocks[i] = create_each_block$1(get_each_context$1(ctx, each_value, i));
    	}

    	const block = {
    		c: function create() {
    			label_1 = element("label");
    			create_component(labeltext.$$.fragment);
    			t = space();
    			select = element("select");

    			for (let i = 0; i < each_blocks.length; i += 1) {
    				each_blocks[i].c();
    			}

    			if (/*$options*/ ctx[1][/*optionKey*/ ctx[0]] === void 0) add_render_callback(() => /*select_change_handler*/ ctx[10].call(select));
    			add_location(select, file$1, 79, 4, 2572);
    			add_location(label_1, file$1, 77, 3, 2530);
    		},
    		m: function mount(target, anchor) {
    			insert_dev(target, label_1, anchor);
    			mount_component(labeltext, label_1, null);
    			append_dev(label_1, t);
    			append_dev(label_1, select);

    			for (let i = 0; i < each_blocks.length; i += 1) {
    				if (each_blocks[i]) {
    					each_blocks[i].m(select, null);
    				}
    			}

    			select_option(select, /*$options*/ ctx[1][/*optionKey*/ ctx[0]], true);
    			current = true;

    			if (!mounted) {
    				dispose = [
    					listen_dev(select, "change", /*select_change_handler*/ ctx[10]),
    					listen_dev(select, "change", /*change_handler_1*/ ctx[11], false, false, false, false)
    				];

    				mounted = true;
    			}
    		},
    		p: function update(ctx, dirty) {
    			const labeltext_changes = {};
    			if (dirty & /*optionKey*/ 1) labeltext_changes.optionKey = /*optionKey*/ ctx[0];
    			labeltext.$set(labeltext_changes);

    			if (dirty & /*Object, settings, optionKey*/ 1) {
    				each_value = Object.entries(settings[/*optionKey*/ ctx[0]].options);
    				validate_each_argument(each_value);
    				let i;

    				for (i = 0; i < each_value.length; i += 1) {
    					const child_ctx = get_each_context$1(ctx, each_value, i);

    					if (each_blocks[i]) {
    						each_blocks[i].p(child_ctx, dirty);
    					} else {
    						each_blocks[i] = create_each_block$1(child_ctx);
    						each_blocks[i].c();
    						each_blocks[i].m(select, null);
    					}
    				}

    				for (; i < each_blocks.length; i += 1) {
    					each_blocks[i].d(1);
    				}

    				each_blocks.length = each_value.length;
    			}

    			if (dirty & /*$options, optionKey, Object, settings*/ 3) {
    				select_option(select, /*$options*/ ctx[1][/*optionKey*/ ctx[0]]);
    			}
    		},
    		i: function intro(local) {
    			if (current) return;
    			transition_in(labeltext.$$.fragment, local);
    			current = true;
    		},
    		o: function outro(local) {
    			transition_out(labeltext.$$.fragment, local);
    			current = false;
    		},
    		d: function destroy(detaching) {
    			if (detaching) detach_dev(label_1);
    			destroy_component(labeltext);
    			destroy_each(each_blocks, detaching);
    			mounted = false;
    			run_all(dispose);
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_if_block_3.name,
    		type: "if",
    		source: "(76:30) ",
    		ctx
    	});

    	return block;
    }

    // (69:31) 
    function create_if_block_2(ctx) {
    	let label_1;
    	let input;
    	let t;
    	let labeltext;
    	let current;
    	let mounted;
    	let dispose;

    	labeltext = new Labeltext({
    			props: { optionKey: /*optionKey*/ ctx[0] },
    			$$inline: true
    		});

    	const block = {
    		c: function create() {
    			label_1 = element("label");
    			input = element("input");
    			t = space();
    			create_component(labeltext.$$.fragment);
    			attr_dev(input, "type", "checkbox");
    			add_location(input, file$1, 71, 4, 2358);
    			add_location(label_1, file$1, 70, 3, 2346);
    		},
    		m: function mount(target, anchor) {
    			insert_dev(target, label_1, anchor);
    			append_dev(label_1, input);
    			input.checked = /*$options*/ ctx[1][/*optionKey*/ ctx[0]];
    			append_dev(label_1, t);
    			mount_component(labeltext, label_1, null);
    			current = true;

    			if (!mounted) {
    				dispose = [
    					listen_dev(input, "change", /*input_change_handler*/ ctx[8]),
    					listen_dev(input, "change", /*change_handler*/ ctx[9], false, false, false, false)
    				];

    				mounted = true;
    			}
    		},
    		p: function update(ctx, dirty) {
    			if (dirty & /*$options, optionKey, Object, settings*/ 3) {
    				input.checked = /*$options*/ ctx[1][/*optionKey*/ ctx[0]];
    			}

    			const labeltext_changes = {};
    			if (dirty & /*optionKey*/ 1) labeltext_changes.optionKey = /*optionKey*/ ctx[0];
    			labeltext.$set(labeltext_changes);
    		},
    		i: function intro(local) {
    			if (current) return;
    			transition_in(labeltext.$$.fragment, local);
    			current = true;
    		},
    		o: function outro(local) {
    			transition_out(labeltext.$$.fragment, local);
    			current = false;
    		},
    		d: function destroy(detaching) {
    			if (detaching) detach_dev(label_1);
    			destroy_component(labeltext);
    			mounted = false;
    			run_all(dispose);
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_if_block_2.name,
    		type: "if",
    		source: "(69:31) ",
    		ctx
    	});

    	return block;
    }

    // (62:2) {#if 'string' === type}
    function create_if_block_1(ctx) {
    	let label_1;
    	let labeltext;
    	let t;
    	let input;
    	let current;
    	let mounted;
    	let dispose;

    	labeltext = new Labeltext({
    			props: { optionKey: /*optionKey*/ ctx[0] },
    			$$inline: true
    		});

    	const block = {
    		c: function create() {
    			label_1 = element("label");
    			create_component(labeltext.$$.fragment);
    			t = space();
    			input = element("input");
    			attr_dev(input, "type", "text");
    			attr_dev(input, "class", "large-text");
    			add_location(input, file$1, 65, 4, 2178);
    			add_location(label_1, file$1, 63, 3, 2136);
    		},
    		m: function mount(target, anchor) {
    			insert_dev(target, label_1, anchor);
    			mount_component(labeltext, label_1, null);
    			append_dev(label_1, t);
    			append_dev(label_1, input);
    			set_input_value(input, /*$options*/ ctx[1][/*optionKey*/ ctx[0]]);
    			current = true;

    			if (!mounted) {
    				dispose = [
    					listen_dev(input, "input", /*input_input_handler*/ ctx[6]),
    					listen_dev(input, "input", /*input_handler*/ ctx[7], false, false, false, false)
    				];

    				mounted = true;
    			}
    		},
    		p: function update(ctx, dirty) {
    			const labeltext_changes = {};
    			if (dirty & /*optionKey*/ 1) labeltext_changes.optionKey = /*optionKey*/ ctx[0];
    			labeltext.$set(labeltext_changes);

    			if (dirty & /*$options, optionKey, Object, settings*/ 3 && input.value !== /*$options*/ ctx[1][/*optionKey*/ ctx[0]]) {
    				set_input_value(input, /*$options*/ ctx[1][/*optionKey*/ ctx[0]]);
    			}
    		},
    		i: function intro(local) {
    			if (current) return;
    			transition_in(labeltext.$$.fragment, local);
    			current = true;
    		},
    		o: function outro(local) {
    			transition_out(labeltext.$$.fragment, local);
    			current = false;
    		},
    		d: function destroy(detaching) {
    			if (detaching) detach_dev(label_1);
    			destroy_component(labeltext);
    			mounted = false;
    			run_all(dispose);
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_if_block_1.name,
    		type: "if",
    		source: "(62:2) {#if 'string' === type}",
    		ctx
    	});

    	return block;
    }

    // (81:5) {#each Object.entries(settings[optionKey].options) as [ selectKey, selectLabel ] }
    function create_each_block$1(ctx) {
    	let option;
    	let t0_value = /*selectLabel*/ ctx[23] + "";
    	let t0;
    	let t1;
    	let option_value_value;

    	const block = {
    		c: function create() {
    			option = element("option");
    			t0 = text(t0_value);
    			t1 = space();
    			option.__value = option_value_value = /*selectKey*/ ctx[22];
    			option.value = option.__value;
    			add_location(option, file$1, 81, 6, 2745);
    		},
    		m: function mount(target, anchor) {
    			insert_dev(target, option, anchor);
    			append_dev(option, t0);
    			append_dev(option, t1);
    		},
    		p: function update(ctx, dirty) {
    			if (dirty & /*optionKey*/ 1 && t0_value !== (t0_value = /*selectLabel*/ ctx[23] + "")) set_data_dev(t0, t0_value);

    			if (dirty & /*optionKey, Object, settings*/ 1 && option_value_value !== (option_value_value = /*selectKey*/ ctx[22])) {
    				prop_dev(option, "__value", option_value_value);
    				option.value = option.__value;
    			}
    		},
    		d: function destroy(detaching) {
    			if (detaching) detach_dev(option);
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_each_block$1.name,
    		type: "each",
    		source: "(81:5) {#each Object.entries(settings[optionKey].options) as [ selectKey, selectLabel ] }",
    		ctx
    	});

    	return block;
    }

    // (103:1) {#if description }
    function create_if_block$1(ctx) {
    	let p;

    	const block = {
    		c: function create() {
    			p = element("p");
    			p.textContent = `${/*description*/ ctx[2]}`;
    			add_location(p, file$1, 103, 2, 3133);
    		},
    		m: function mount(target, anchor) {
    			insert_dev(target, p, anchor);
    		},
    		p: noop,
    		d: function destroy(detaching) {
    			if (detaching) detach_dev(p);
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_if_block$1.name,
    		type: "if",
    		source: "(103:1) {#if description }",
    		ctx
    	});

    	return block;
    }

    function create_fragment$1(ctx) {
    	let div;
    	let p;
    	let current_block_type_index;
    	let if_block0;
    	let t0;
    	let t1;
    	let hr;
    	let current;

    	const if_block_creators = [
    		create_if_block_1,
    		create_if_block_2,
    		create_if_block_3,
    		create_if_block_4,
    		create_else_block
    	];

    	const if_blocks = [];

    	function select_block_type(ctx, dirty) {
    		if ('string' === /*type*/ ctx[3]) return 0;
    		if ('boolean' === /*type*/ ctx[3]) return 1;
    		if ('select' === /*type*/ ctx[3]) return 2;
    		if ('integer' === /*type*/ ctx[3]) return 3;
    		return 4;
    	}

    	current_block_type_index = select_block_type(ctx);
    	if_block0 = if_blocks[current_block_type_index] = if_block_creators[current_block_type_index](ctx);
    	let if_block1 = /*description*/ ctx[2] && create_if_block$1(ctx);

    	const block = {
    		c: function create() {
    			div = element("div");
    			p = element("p");
    			if_block0.c();
    			t0 = space();
    			if (if_block1) if_block1.c();
    			t1 = space();
    			hr = element("hr");
    			add_location(p, file$1, 60, 1, 2102);
    			add_location(hr, file$1, 107, 1, 3169);
    			add_location(div, file$1, 59, 0, 2095);
    		},
    		l: function claim(nodes) {
    			throw new Error("options.hydrate only works if the component was compiled with the `hydratable: true` option");
    		},
    		m: function mount(target, anchor) {
    			insert_dev(target, div, anchor);
    			append_dev(div, p);
    			if_blocks[current_block_type_index].m(p, null);
    			append_dev(div, t0);
    			if (if_block1) if_block1.m(div, null);
    			append_dev(div, t1);
    			append_dev(div, hr);
    			current = true;
    		},
    		p: function update(ctx, [dirty]) {
    			if_block0.p(ctx, dirty);
    			if (/*description*/ ctx[2]) if_block1.p(ctx, dirty);
    		},
    		i: function intro(local) {
    			if (current) return;
    			transition_in(if_block0);
    			current = true;
    		},
    		o: function outro(local) {
    			transition_out(if_block0);
    			current = false;
    		},
    		d: function destroy(detaching) {
    			if (detaching) detach_dev(div);
    			if_blocks[current_block_type_index].d();
    			if (if_block1) if_block1.d();
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_fragment$1.name,
    		type: "component",
    		source: "",
    		ctx
    	});

    	return block;
    }

    function instance$1($$self, $$props, $$invalidate) {
    	let $message;
    	let $options;
    	validate_store(message, 'message');
    	component_subscribe($$self, message, $$value => $$invalidate(16, $message = $$value));
    	validate_store(options, 'options');
    	component_subscribe($$self, options, $$value => $$invalidate(1, $options = $$value));
    	let { $$slots: slots = {}, $$scope } = $$props;
    	validate_slots('Setting', slots, []);
    	const { log } = console;
    	let { optionKey } = $$props;
    	const description = settings[optionKey].description;
    	const label = settings[optionKey].label;
    	const type = settings[optionKey].type;
    	const selectOptions = settings[optionKey].options;
    	const premiumUrl = 'https://nextgenthemes.com/plugins/arve-' + settings[optionKey].tag;
    	const sectionLabel = sections[settings[optionKey].tag];
    	let isSaving = false;
    	let textInputTimeout;

    	function debouncedSaveOptions() {
    		if (textInputTimeout) {
    			clearTimeout(textInputTimeout);
    		}

    		textInputTimeout = setTimeout(saveOptions, 300);
    	}

    	function saveOptions(refreshAfterSave = false) {
    		if (isSaving) {
    			set_store_value(message, $message = 'trying to save too fast', $message);
    			return;
    		}

    		// set the state so that another save cannot happen while processing
    		isSaving = true;

    		set_store_value(message, $message = 'Saving...', $message);

    		// Make a POST request to the REST API route that we registered in our PHP file
    		window.jQuery.ajax({
    			url: restURL + '/save',
    			method: 'POST',
    			data: $options,
    			// set the nonce in the request header
    			beforeSend(request) {
    				request.setRequestHeader('X-WP-Nonce', nonce);
    			},
    			// callback to run upon successful completion of our request
    			success: () => {
    				log('success');
    				set_store_value(message, $message = 'Options saved', $message);
    				setTimeout(() => set_store_value(message, $message = '', $message), 1000);
    			},
    			// callback to run if our request caused an error
    			error: errorData => {
    				set_store_value(message, $message = errorData.responseText, $message);
    				refreshAfterSave = false;
    			},
    			// when our request is complete (successful or not), reset the state to indicate we are no longer saving
    			complete: () => {
    				log('complete');
    				isSaving = false;

    				if (refreshAfterSave) {
    					refreshAfterSave = false;
    					window.location.reload();
    				}
    			}
    		});
    	}

    	$$self.$$.on_mount.push(function () {
    		if (optionKey === undefined && !('optionKey' in $$props || $$self.$$.bound[$$self.$$.props['optionKey']])) {
    			console_1.warn("<Setting> was created without expected prop 'optionKey'");
    		}
    	});

    	const writable_props = ['optionKey'];

    	Object_1$1.keys($$props).forEach(key => {
    		if (!~writable_props.indexOf(key) && key.slice(0, 2) !== '$$' && key !== 'slot') console_1.warn(`<Setting> was created with unknown prop '${key}'`);
    	});

    	function input_input_handler() {
    		$options[optionKey] = this.value;
    		options.set($options);
    		$$invalidate(0, optionKey);
    	}

    	const input_handler = () => {
    		debouncedSaveOptions();
    	};

    	function input_change_handler() {
    		$options[optionKey] = this.checked;
    		options.set($options);
    		$$invalidate(0, optionKey);
    	}

    	const change_handler = () => {
    		saveOptions();
    	};

    	function select_change_handler() {
    		$options[optionKey] = select_value(this);
    		options.set($options);
    		$$invalidate(0, optionKey);
    	}

    	const change_handler_1 = () => {
    		saveOptions();
    	};

    	function input_input_handler_1() {
    		$options[optionKey] = to_number(this.value);
    		options.set($options);
    		$$invalidate(0, optionKey);
    	}

    	const input_handler_1 = () => {
    		debouncedSaveOptions();
    	};

    	$$self.$$set = $$props => {
    		if ('optionKey' in $$props) $$invalidate(0, optionKey = $$props.optionKey);
    	};

    	$$self.$capture_state = () => ({
    		options,
    		settings,
    		sections,
    		restURL,
    		nonce,
    		message,
    		Labeltext,
    		log,
    		optionKey,
    		description,
    		label,
    		type,
    		selectOptions,
    		premiumUrl,
    		sectionLabel,
    		isSaving,
    		textInputTimeout,
    		debouncedSaveOptions,
    		saveOptions,
    		$message,
    		$options
    	});

    	$$self.$inject_state = $$props => {
    		if ('optionKey' in $$props) $$invalidate(0, optionKey = $$props.optionKey);
    		if ('isSaving' in $$props) isSaving = $$props.isSaving;
    		if ('textInputTimeout' in $$props) textInputTimeout = $$props.textInputTimeout;
    	};

    	if ($$props && "$$inject" in $$props) {
    		$$self.$inject_state($$props.$$inject);
    	}

    	return [
    		optionKey,
    		$options,
    		description,
    		type,
    		debouncedSaveOptions,
    		saveOptions,
    		input_input_handler,
    		input_handler,
    		input_change_handler,
    		change_handler,
    		select_change_handler,
    		change_handler_1,
    		input_input_handler_1,
    		input_handler_1
    	];
    }

    class Setting extends SvelteComponentDev {
    	constructor(options) {
    		super(options);
    		init(this, options, instance$1, create_fragment$1, safe_not_equal, { optionKey: 0 });

    		dispatch_dev("SvelteRegisterComponent", {
    			component: this,
    			tagName: "Setting",
    			options,
    			id: create_fragment$1.name
    		});
    	}

    	get optionKey() {
    		throw new Error("<Setting>: Props cannot be read directly from the component instance unless compiling with 'accessors: true' or '<svelte:options accessors/>'");
    	}

    	set optionKey(value) {
    		throw new Error("<Setting>: Props cannot be set directly on the component instance unless compiling with 'accessors: true' or '<svelte:options accessors/>'");
    	}
    }

    /* src/App.svelte generated by Svelte v3.58.0 */

    const { Object: Object_1 } = globals;
    const file = "src/App.svelte";

    function get_each_context(ctx, list, i) {
    	const child_ctx = ctx.slice();
    	child_ctx[7] = list[i][0];
    	child_ctx[8] = list[i][1];
    	return child_ctx;
    }

    function get_each_context_1(ctx, list, i) {
    	const child_ctx = ctx.slice();
    	child_ctx[11] = list[i];
    	return child_ctx;
    }

    function get_each_context_2(ctx, list, i) {
    	const child_ctx = ctx.slice();
    	child_ctx[7] = list[i][0];
    	child_ctx[8] = list[i][1];
    	return child_ctx;
    }

    // (48:1) {#each Object.entries(sections) as [ sectionKey, sectionLabel ] }
    function create_each_block_2(ctx) {
    	let button;
    	let t0_value = /*sectionLabel*/ ctx[8] + "";
    	let t0;
    	let t1;
    	let mounted;
    	let dispose;

    	function click_handler_1() {
    		return /*click_handler_1*/ ctx[5](/*sectionKey*/ ctx[7]);
    	}

    	const block = {
    		c: function create() {
    			button = element("button");
    			t0 = text(t0_value);
    			t1 = space();
    			attr_dev(button, "class", "nav-tab svelte-3ssptq");
    			toggle_class(button, "nav-tab-active", /*sectionKey*/ ctx[7] === /*activeSection*/ ctx[0]);
    			add_location(button, file, 48, 2, 1620);
    		},
    		m: function mount(target, anchor) {
    			insert_dev(target, button, anchor);
    			append_dev(button, t0);
    			append_dev(button, t1);

    			if (!mounted) {
    				dispose = listen_dev(button, "click", click_handler_1, false, false, false, false);
    				mounted = true;
    			}
    		},
    		p: function update(new_ctx, dirty) {
    			ctx = new_ctx;

    			if (dirty & /*Object, sections, activeSection*/ 1) {
    				toggle_class(button, "nav-tab-active", /*sectionKey*/ ctx[7] === /*activeSection*/ ctx[0]);
    			}
    		},
    		d: function destroy(detaching) {
    			if (detaching) detach_dev(button);
    			mounted = false;
    			dispose();
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_each_block_2.name,
    		type: "each",
    		source: "(48:1) {#each Object.entries(sections) as [ sectionKey, sectionLabel ] }",
    		ctx
    	});

    	return block;
    }

    // (67:5) {#if settings[optionKey].tag === sectionKey}
    function create_if_block(ctx) {
    	let setting;
    	let t;
    	let current;

    	setting = new Setting({
    			props: { optionKey: /*optionKey*/ ctx[11] },
    			$$inline: true
    		});

    	const block = {
    		c: function create() {
    			create_component(setting.$$.fragment);
    			t = space();
    		},
    		m: function mount(target, anchor) {
    			mount_component(setting, target, anchor);
    			insert_dev(target, t, anchor);
    			current = true;
    		},
    		p: function update(ctx, dirty) {
    			const setting_changes = {};
    			if (dirty & /*$options*/ 2) setting_changes.optionKey = /*optionKey*/ ctx[11];
    			setting.$set(setting_changes);
    		},
    		i: function intro(local) {
    			if (current) return;
    			transition_in(setting.$$.fragment, local);
    			current = true;
    		},
    		o: function outro(local) {
    			transition_out(setting.$$.fragment, local);
    			current = false;
    		},
    		d: function destroy(detaching) {
    			destroy_component(setting, detaching);
    			if (detaching) detach_dev(t);
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_if_block.name,
    		type: "if",
    		source: "(67:5) {#if settings[optionKey].tag === sectionKey}",
    		ctx
    	});

    	return block;
    }

    // (65:4) {#each Object.keys($options) as optionKey }
    function create_each_block_1(ctx) {
    	let if_block_anchor;
    	let current;
    	let if_block = settings[/*optionKey*/ ctx[11]].tag === /*sectionKey*/ ctx[7] && create_if_block(ctx);

    	const block = {
    		c: function create() {
    			if (if_block) if_block.c();
    			if_block_anchor = empty();
    		},
    		m: function mount(target, anchor) {
    			if (if_block) if_block.m(target, anchor);
    			insert_dev(target, if_block_anchor, anchor);
    			current = true;
    		},
    		p: function update(ctx, dirty) {
    			if (settings[/*optionKey*/ ctx[11]].tag === /*sectionKey*/ ctx[7]) {
    				if (if_block) {
    					if_block.p(ctx, dirty);

    					if (dirty & /*$options*/ 2) {
    						transition_in(if_block, 1);
    					}
    				} else {
    					if_block = create_if_block(ctx);
    					if_block.c();
    					transition_in(if_block, 1);
    					if_block.m(if_block_anchor.parentNode, if_block_anchor);
    				}
    			} else if (if_block) {
    				group_outros();

    				transition_out(if_block, 1, 1, () => {
    					if_block = null;
    				});

    				check_outros();
    			}
    		},
    		i: function intro(local) {
    			if (current) return;
    			transition_in(if_block);
    			current = true;
    		},
    		o: function outro(local) {
    			transition_out(if_block);
    			current = false;
    		},
    		d: function destroy(detaching) {
    			if (if_block) if_block.d(detaching);
    			if (detaching) detach_dev(if_block_anchor);
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_each_block_1.name,
    		type: "each",
    		source: "(65:4) {#each Object.keys($options) as optionKey }",
    		ctx
    	});

    	return block;
    }

    // (59:2) {#each Object.entries(sections) as [ sectionKey, sectionLabel ] }
    function create_each_block(ctx) {
    	let div;
    	let h1;
    	let t0_value = /*sectionLabel*/ ctx[8] + "";
    	let t0;
    	let h1_hidden_value;
    	let t1;
    	let div_hidden_value;
    	let t2;
    	let current;
    	let each_value_1 = Object.keys(/*$options*/ ctx[1]);
    	validate_each_argument(each_value_1);
    	let each_blocks = [];

    	for (let i = 0; i < each_value_1.length; i += 1) {
    		each_blocks[i] = create_each_block_1(get_each_context_1(ctx, each_value_1, i));
    	}

    	const out = i => transition_out(each_blocks[i], 1, 1, () => {
    		each_blocks[i] = null;
    	});

    	const block = {
    		c: function create() {
    			div = element("div");
    			h1 = element("h1");
    			t0 = text(t0_value);
    			t1 = space();

    			for (let i = 0; i < each_blocks.length; i += 1) {
    				each_blocks[i].c();
    			}

    			t2 = space();
    			h1.hidden = h1_hidden_value = 'all' != /*activeSection*/ ctx[0];
    			attr_dev(h1, "class", "svelte-3ssptq");
    			add_location(h1, file, 62, 4, 2064);
    			attr_dev(div, "class", "ngt-section ngt-section--" + /*sectionKey*/ ctx[7] + " svelte-3ssptq");
    			div.hidden = div_hidden_value = 'all' != /*activeSection*/ ctx[0] && /*sectionKey*/ ctx[7] != /*activeSection*/ ctx[0];
    			add_location(div, file, 60, 3, 1940);
    		},
    		m: function mount(target, anchor) {
    			insert_dev(target, div, anchor);
    			append_dev(div, h1);
    			append_dev(h1, t0);
    			append_dev(div, t1);

    			for (let i = 0; i < each_blocks.length; i += 1) {
    				if (each_blocks[i]) {
    					each_blocks[i].m(div, null);
    				}
    			}

    			insert_dev(target, t2, anchor);
    			current = true;
    		},
    		p: function update(ctx, dirty) {
    			if (!current || dirty & /*activeSection*/ 1 && h1_hidden_value !== (h1_hidden_value = 'all' != /*activeSection*/ ctx[0])) {
    				prop_dev(h1, "hidden", h1_hidden_value);
    			}

    			if (dirty & /*Object, $options, settings, sections*/ 2) {
    				each_value_1 = Object.keys(/*$options*/ ctx[1]);
    				validate_each_argument(each_value_1);
    				let i;

    				for (i = 0; i < each_value_1.length; i += 1) {
    					const child_ctx = get_each_context_1(ctx, each_value_1, i);

    					if (each_blocks[i]) {
    						each_blocks[i].p(child_ctx, dirty);
    						transition_in(each_blocks[i], 1);
    					} else {
    						each_blocks[i] = create_each_block_1(child_ctx);
    						each_blocks[i].c();
    						transition_in(each_blocks[i], 1);
    						each_blocks[i].m(div, null);
    					}
    				}

    				group_outros();

    				for (i = each_value_1.length; i < each_blocks.length; i += 1) {
    					out(i);
    				}

    				check_outros();
    			}

    			if (!current || dirty & /*activeSection*/ 1 && div_hidden_value !== (div_hidden_value = 'all' != /*activeSection*/ ctx[0] && /*sectionKey*/ ctx[7] != /*activeSection*/ ctx[0])) {
    				prop_dev(div, "hidden", div_hidden_value);
    			}
    		},
    		i: function intro(local) {
    			if (current) return;

    			for (let i = 0; i < each_value_1.length; i += 1) {
    				transition_in(each_blocks[i]);
    			}

    			current = true;
    		},
    		o: function outro(local) {
    			each_blocks = each_blocks.filter(Boolean);

    			for (let i = 0; i < each_blocks.length; i += 1) {
    				transition_out(each_blocks[i]);
    			}

    			current = false;
    		},
    		d: function destroy(detaching) {
    			if (detaching) detach_dev(div);
    			destroy_each(each_blocks, detaching);
    			if (detaching) detach_dev(t2);
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_each_block.name,
    		type: "each",
    		source: "(59:2) {#each Object.entries(sections) as [ sectionKey, sectionLabel ] }",
    		ctx
    	});

    	return block;
    }

    function create_fragment(ctx) {
    	let h2;
    	let button;
    	let t1;
    	let t2;
    	let div2;
    	let div0;
    	let t3;
    	let p0;
    	let strong;
    	let t4;
    	let t5;
    	let t6;
    	let p1;
    	let t7_value = JSON.stringify(/*$options*/ ctx[1], 0, 2) + "";
    	let t7;
    	let t8;
    	let div1;
    	let current;
    	let mounted;
    	let dispose;
    	let each_value_2 = Object.entries(sections);
    	validate_each_argument(each_value_2);
    	let each_blocks_1 = [];

    	for (let i = 0; i < each_value_2.length; i += 1) {
    		each_blocks_1[i] = create_each_block_2(get_each_context_2(ctx, each_value_2, i));
    	}

    	let each_value = Object.entries(sections);
    	validate_each_argument(each_value);
    	let each_blocks = [];

    	for (let i = 0; i < each_value.length; i += 1) {
    		each_blocks[i] = create_each_block(get_each_context(ctx, each_value, i));
    	}

    	const out = i => transition_out(each_blocks[i], 1, 1, () => {
    		each_blocks[i] = null;
    	});

    	const block = {
    		c: function create() {
    			h2 = element("h2");
    			button = element("button");
    			button.textContent = "All";
    			t1 = space();

    			for (let i = 0; i < each_blocks_1.length; i += 1) {
    				each_blocks_1[i].c();
    			}

    			t2 = space();
    			div2 = element("div");
    			div0 = element("div");

    			for (let i = 0; i < each_blocks.length; i += 1) {
    				each_blocks[i].c();
    			}

    			t3 = space();
    			p0 = element("p");
    			strong = element("strong");
    			t4 = text(/*$message*/ ctx[2]);
    			t5 = text(" ");
    			t6 = space();
    			p1 = element("p");
    			t7 = text(t7_value);
    			t8 = space();
    			div1 = element("div");
    			attr_dev(button, "class", "nav-tab svelte-3ssptq");
    			toggle_class(button, "nav-tab-active", 'all' === /*activeSection*/ ctx[0]);
    			add_location(button, file, 44, 1, 1420);
    			attr_dev(h2, "class", "nav-tab-wrapper");
    			add_location(h2, file, 43, 0, 1390);
    			add_location(strong, file, 79, 3, 2366);
    			add_location(p0, file, 78, 2, 2359);
    			add_location(p1, file, 82, 2, 2410);
    			attr_dev(div0, "class", "ngt-settings-grid__content svelte-3ssptq");
    			add_location(div0, file, 56, 1, 1824);
    			attr_dev(div1, "class", "ngt-settings-grid__sidebar svelte-3ssptq");
    			add_location(div1, file, 86, 1, 2463);
    			attr_dev(div2, "class", "ngt-settings-grid svelte-3ssptq");
    			add_location(div2, file, 54, 0, 1790);
    		},
    		l: function claim(nodes) {
    			throw new Error("options.hydrate only works if the component was compiled with the `hydratable: true` option");
    		},
    		m: function mount(target, anchor) {
    			insert_dev(target, h2, anchor);
    			append_dev(h2, button);
    			append_dev(h2, t1);

    			for (let i = 0; i < each_blocks_1.length; i += 1) {
    				if (each_blocks_1[i]) {
    					each_blocks_1[i].m(h2, null);
    				}
    			}

    			insert_dev(target, t2, anchor);
    			insert_dev(target, div2, anchor);
    			append_dev(div2, div0);

    			for (let i = 0; i < each_blocks.length; i += 1) {
    				if (each_blocks[i]) {
    					each_blocks[i].m(div0, null);
    				}
    			}

    			append_dev(div0, t3);
    			append_dev(div0, p0);
    			append_dev(p0, strong);
    			append_dev(strong, t4);
    			append_dev(strong, t5);
    			append_dev(div0, t6);
    			append_dev(div0, p1);
    			append_dev(p1, t7);
    			append_dev(div2, t8);
    			append_dev(div2, div1);
    			current = true;

    			if (!mounted) {
    				dispose = listen_dev(button, "click", /*click_handler*/ ctx[4], false, false, false, false);
    				mounted = true;
    			}
    		},
    		p: function update(ctx, [dirty]) {
    			if (!current || dirty & /*activeSection*/ 1) {
    				toggle_class(button, "nav-tab-active", 'all' === /*activeSection*/ ctx[0]);
    			}

    			if (dirty & /*Object, sections, activeSection, showSection*/ 9) {
    				each_value_2 = Object.entries(sections);
    				validate_each_argument(each_value_2);
    				let i;

    				for (i = 0; i < each_value_2.length; i += 1) {
    					const child_ctx = get_each_context_2(ctx, each_value_2, i);

    					if (each_blocks_1[i]) {
    						each_blocks_1[i].p(child_ctx, dirty);
    					} else {
    						each_blocks_1[i] = create_each_block_2(child_ctx);
    						each_blocks_1[i].c();
    						each_blocks_1[i].m(h2, null);
    					}
    				}

    				for (; i < each_blocks_1.length; i += 1) {
    					each_blocks_1[i].d(1);
    				}

    				each_blocks_1.length = each_value_2.length;
    			}

    			if (dirty & /*Object, sections, activeSection, $options, settings*/ 3) {
    				each_value = Object.entries(sections);
    				validate_each_argument(each_value);
    				let i;

    				for (i = 0; i < each_value.length; i += 1) {
    					const child_ctx = get_each_context(ctx, each_value, i);

    					if (each_blocks[i]) {
    						each_blocks[i].p(child_ctx, dirty);
    						transition_in(each_blocks[i], 1);
    					} else {
    						each_blocks[i] = create_each_block(child_ctx);
    						each_blocks[i].c();
    						transition_in(each_blocks[i], 1);
    						each_blocks[i].m(div0, t3);
    					}
    				}

    				group_outros();

    				for (i = each_value.length; i < each_blocks.length; i += 1) {
    					out(i);
    				}

    				check_outros();
    			}

    			if (!current || dirty & /*$message*/ 4) set_data_dev(t4, /*$message*/ ctx[2]);
    			if ((!current || dirty & /*$options*/ 2) && t7_value !== (t7_value = JSON.stringify(/*$options*/ ctx[1], 0, 2) + "")) set_data_dev(t7, t7_value);
    		},
    		i: function intro(local) {
    			if (current) return;

    			for (let i = 0; i < each_value.length; i += 1) {
    				transition_in(each_blocks[i]);
    			}

    			current = true;
    		},
    		o: function outro(local) {
    			each_blocks = each_blocks.filter(Boolean);

    			for (let i = 0; i < each_blocks.length; i += 1) {
    				transition_out(each_blocks[i]);
    			}

    			current = false;
    		},
    		d: function destroy(detaching) {
    			if (detaching) detach_dev(h2);
    			destroy_each(each_blocks_1, detaching);
    			if (detaching) detach_dev(t2);
    			if (detaching) detach_dev(div2);
    			destroy_each(each_blocks, detaching);
    			mounted = false;
    			dispose();
    		}
    	};

    	dispatch_dev("SvelteRegisterBlock", {
    		block,
    		id: create_fragment.name,
    		type: "component",
    		source: "",
    		ctx
    	});

    	return block;
    }

    function injectFromTemplates() {
    	const templates = document.querySelectorAll('template[data-ngt-svelte-target]');

    	templates.forEach(template => {
    		const target = document.querySelector(template.dataset.ngtSvelteTarget);

    		if (!target) {
    			return;
    		}

    		if (template.dataset.append) {
    			target.append(template.content.cloneNode(true));
    		} else {
    			target.prepend(template.content.cloneNode(true));
    		}
    	});
    }

    function instance($$self, $$props, $$invalidate) {
    	let $options;
    	let $message;
    	validate_store(options, 'options');
    	component_subscribe($$self, options, $$value => $$invalidate(1, $options = $$value));
    	validate_store(message, 'message');
    	component_subscribe($$self, message, $$value => $$invalidate(2, $message = $$value));
    	let { $$slots: slots = {}, $$scope } = $$props;
    	validate_slots('App', slots, []);
    	let activeSection = 'all';

    	function showSection(sectionKey) {
    		$$invalidate(0, activeSection = sectionKey);
    	}

    	onMount(async () => {
    		injectFromTemplates();
    	});

    	function uploadImage(optionKey) {

    		const image = window.wp.media({ title: 'Upload Image', multiple: false }).open().on('select', function () {
    			// This will return the selected image from the Media Uploader, the result is an object
    			const uploadedImage = image.state().get('selection').first();

    			// We convert uploadedImage to a JSON object to make accessing it easier
    			const attachmentID = uploadedImage.toJSON().id;

    			set_store_value(options, $options[optionKey] = attachmentID, $options);
    		});
    	}

    	const writable_props = [];

    	Object_1.keys($$props).forEach(key => {
    		if (!~writable_props.indexOf(key) && key.slice(0, 2) !== '$$' && key !== 'slot') console.warn(`<App> was created with unknown prop '${key}'`);
    	});

    	const click_handler = () => showSection('all');
    	const click_handler_1 = sectionKey => showSection(sectionKey);

    	$$self.$capture_state = () => ({
    		options,
    		settings,
    		sections,
    		message,
    		onMount,
    		Setting,
    		activeSection,
    		showSection,
    		injectFromTemplates,
    		uploadImage,
    		$options,
    		$message
    	});

    	$$self.$inject_state = $$props => {
    		if ('activeSection' in $$props) $$invalidate(0, activeSection = $$props.activeSection);
    	};

    	if ($$props && "$$inject" in $$props) {
    		$$self.$inject_state($$props.$$inject);
    	}

    	return [activeSection, $options, $message, showSection, click_handler, click_handler_1];
    }

    class App extends SvelteComponentDev {
    	constructor(options) {
    		super(options);
    		init(this, options, instance, create_fragment, safe_not_equal, {});

    		dispatch_dev("SvelteRegisterComponent", {
    			component: this,
    			tagName: "App",
    			options,
    			id: create_fragment.name
    		});
    	}
    }

    const app = new App({
        target: document.querySelector('#ngt-settings-svelte'),
    });

    return app;

})();
//# sourceMappingURL=bundle.js.map
