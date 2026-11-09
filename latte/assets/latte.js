(function () {
    'use strict';

    const { createElement: h, createRoot, useState, useId } = window.wp.element;
    // Sample origins: replace these with your actual five coffees.
    const origins = ['Ethiopia', 'Colombia', 'Guatemala', 'Kenya', 'Brazil'];
    const flavors = ['Simple', 'Honey', 'Strawberry', 'Dulce de leche', 'Basil'];

    function Latte() {
        const id = useId();
        const [size, setSize] = useState('12oz');
        const [espresso, setEspresso] = useState(origins[0]);
        const [syrups, setSyrups] = useState([]);
        const [temperature, setTemperature] = useState('Hot');
        const [service, setService] = useState('To go');

        function choices(title, values, selected, update, multiple) {
            return h('fieldset', { className: 'latte-field' },
                h('legend', null, title),
                h('div', { className: 'latte-options' }, values.map(value => {
                    const checked = multiple ? selected.includes(value) : selected === value;
                    return h('label', { key: value, className: 'latte-option' },
                        h('input', {
                            type: multiple ? 'checkbox' : 'radio',
                            name: id + '-' + title,
                            value, checked,
                            onChange: () => update(multiple
                                ? (checked ? selected.filter(item => item !== value) : [...selected, value])
                                : value)
                        }),
                        h('span', null, value)
                    );
                }))
            );
        }

        return h('section', { className: 'latte-configurator', 'aria-labelledby': id + '-title' },
            h('header', null,
                h('p', { className: 'latte-eyebrow' }, 'MADE YOUR WAY'),
                h('h2', { id: id + '-title' }, 'Build your latte'),
                h('p', null, 'Choose your coffee. Find your flavor.')
            ),
            h('div', { className: 'latte-layout' },
                h('div', null,
                    choices('Size', ['12oz', '16oz'], size, setSize),
                    choices('Single origin espresso', origins, espresso, setEspresso),
                    choices('Syrups · choose any or none', flavors, syrups, setSyrups, true),
                    choices('Temperature', ['Hot', 'Iced'], temperature, setTemperature),
                    choices('Service', ['Here', 'To go'], service, setService)
                ),
                h('aside', { className: 'latte-summary', 'aria-live': 'polite', 'aria-atomic': true },
                    h('h3', null, 'Your latte'),
                    h('p', { className: 'latte-drink' }, size + ' ' + temperature.toLowerCase() + ' latte'),
                    h('dl', null,
                        h('dt', null, 'Espresso'), h('dd', null, espresso),
                        h('dt', null, 'Syrups'), h('dd', null, flavors.filter(flavor => syrups.includes(flavor)).join(', ') || 'No syrup'),
                        h('dt', null, 'Service'), h('dd', null, service)
                    ),
                    h('p', { className: 'latte-note' }, 'Drink preview only. Ordering and payment are not connected.')
                )
            )
        );
    }

    function mount() {
        document.querySelectorAll('.latte-root').forEach(container => {
            if (container.dataset.latteMounted) return;
            container.dataset.latteMounted = 'true';
            createRoot(container).render(h(Latte));
        });
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', mount);
    else mount();
})();
