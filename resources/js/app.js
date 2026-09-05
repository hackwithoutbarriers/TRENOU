document.addEventListener('DOMContentLoaded', () => {
    const menuToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');

    if (menuToggle && mobileMenu) {
        const setMenuState = (isOpen) => {
            menuToggle.setAttribute('aria-expanded', String(isOpen));
            menuToggle.setAttribute('aria-label', isOpen ? 'Fermer le menu' : 'Ouvrir le menu');
            menuToggle.classList.toggle('active', isOpen);
            mobileMenu.classList.toggle('hidden', !isOpen);
        };

        menuToggle.addEventListener('click', () => {
            const isOpen = menuToggle.getAttribute('aria-expanded') === 'true';
            setMenuState(!isOpen);
        });

        mobileMenu.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => setMenuState(false));
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && menuToggle.getAttribute('aria-expanded') === 'true') {
                setMenuState(false);
                menuToggle.focus();
            }
        });
    }

    const quoteConfigBlock = document.getElementById('quote-config');
    if (!quoteConfigBlock) {
        return;
    }

    let quotePayload;

    try {
        quotePayload = JSON.parse(quoteConfigBlock.textContent || '{}');
    } catch (error) {
        quoteConfigBlock.insertAdjacentHTML(
            'afterend',
            '<p role="alert" class="mt-4 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">Le configurateur est temporairement indisponible. Veuillez réessayer dans quelques instants.</p>',
        );
        return;
    }
    const config = quotePayload.config ?? quotePayload;
    const oldValues = quotePayload.old ?? {};
    const form = document.getElementById('quote-configurator-form');
    const stepper = document.getElementById('stepper');
    const panels = document.getElementById('step-panels');
    const previousStepButton = document.getElementById('previous-step');
    const nextStepButton = document.getElementById('next-step');
    const submitQuoteButton = document.getElementById('submit-quote');
    const estimateRange = document.getElementById('estimate-range');
    const estimateMeta = document.getElementById('estimate-meta');
    const validationMessage = document.getElementById('quote-validation-message');

    if (!form || !stepper || !panels || !previousStepButton || !nextStepButton || !submitQuoteButton || !estimateRange || !estimateMeta || !validationMessage) {
        return;
    }

    const state = {
        step: 0,
        categorie: form.querySelector('[name="categorie"]')?.value || '',
        sousType: form.querySelector('[name="sous_type"]')?.value || '',
        dimensions: (() => {
            const raw = form.querySelector('[name="dimensions"]')?.value;

            try {
                return JSON.parse(raw || '{"largeur":"","hauteur":"","longueur":""}');
            } catch {
                return { largeur: '', hauteur: '', longueur: '' };
            }
        })(),
        finition: form.querySelector('[name="finition"]')?.value || config.finitions?.[1]?.id || 'ral',
        vitrage: form.querySelector('[name="vitrage"]')?.value || config.vitrages?.[1]?.id || 'double',
        options: (() => {
            const raw = form.querySelector('[name="options"]')?.value || '[]';

            try {
                return JSON.parse(raw);
            } catch {
                return [];
            }
        })(),
        nom: oldValues.nom || '',
        telephone: oldValues.telephone || '',
        ville: oldValues.ville || '',
        pays: oldValues.pays || 'Togo',
    };

    const moneyFormatter = new Intl.NumberFormat('fr-FR', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    });

    const formatMoney = (value) => `${moneyFormatter.format(Math.round(value))} FCFA`;

    const parseFloatFromInput = (value) => {
        const numeric = Number.parseFloat(String(value).replace(',', '.'));
        return Number.isFinite(numeric) ? numeric : 0;
    };

    const getSubtype = () => {
        if (!state.categorie || !config.subtypes?.[state.categorie]) {
            return null;
        }

        return config.subtypes[state.categorie].find((subtype) => subtype.id === state.sousType) || null;
    };

    const getEstimatedQuantity = () => {
        const subtype = getSubtype();

        if (!subtype) {
            return 0;
        }

        if (subtype.unit === 'm²') {
            return parseFloatFromInput(state.dimensions.largeur) * parseFloatFromInput(state.dimensions.hauteur);
        }

        return parseFloatFromInput(state.dimensions.longueur);
    };

    const getEstimate = () => {
        const subtype = getSubtype();
        const quantity = getEstimatedQuantity();

        if (!subtype || quantity <= 0) {
            return null;
        }

        const finition = config.finitions.find((item) => item.id === state.finition) || config.finitions[0];
        const glazing = subtype.hasGlazing ? (config.vitrages.find((item) => item.id === state.vitrage) || config.vitrages[0]) : { multiplier: 1 };
        let total = subtype.base * quantity * (finition?.multiplier ?? 1) * (glazing?.multiplier ?? 1);

        state.options.forEach((optionId) => {
            const option = config.options.find((item) => item.id === optionId);
            if (!option) {
                return;
            }

            if (option.type === 'flat') {
                total += Number(option.value || 0);
            }

            if (option.type === 'perUnit') {
                total += Number(option.value || 0) * quantity;
            }

            if (option.type === 'percent') {
                total += total * Number(option.value || 0);
            }
        });

        return {
            min: Math.round(total * 0.85),
            max: Math.round(total * 1.15),
            devise: 'FCFA',
        };
    };

    const buildDescription = () => {
        const subtype = getSubtype();
        const category = config.categories.find((item) => item.id === state.categorie);
        const parts = [];

        if (category) {
            parts.push(`Catégorie : ${category.label}`);
        }

        if (subtype) {
            parts.push(`Produit : ${subtype.label}`);
        }

        if (getEstimatedQuantity() > 0) {
            const measure = subtype?.unit === 'm²'
                ? `${state.dimensions.largeur || 0} x ${state.dimensions.hauteur || 0} m`
                : `${state.dimensions.longueur || 0} ml`;
            parts.push(`Dimensions : ${measure}`);
        }

        if (state.finition) {
            const finition = config.finitions.find((item) => item.id === state.finition);
            if (finition) {
                parts.push(`Finition : ${finition.label}`);
            }
        }

        if (subtype?.hasGlazing && state.vitrage) {
            const vitrage = config.vitrages.find((item) => item.id === state.vitrage);
            if (vitrage) {
                parts.push(`Vitrage : ${vitrage.label}`);
            }
        }

        if (state.options.length > 0) {
            const names = state.options.map((optionId) => config.options.find((item) => item.id === optionId)?.label).filter(Boolean);
            if (names.length > 0) {
                parts.push(`Options : ${names.join(', ')}`);
            }
        }

        return parts.length > 0 ? parts.join(' — ') : 'Demande de devis';
    };

    const syncFormValues = () => {
        const formValues = {
            categorie: state.categorie,
            sous_type: state.sousType,
            dimensions: JSON.stringify(state.dimensions),
            finition: state.finition,
            vitrage: state.vitrage,
            options: JSON.stringify(state.options),
            source: 'simulateur',
        };

        Object.entries(formValues).forEach(([name, value]) => {
            const field = form.querySelector(`[name="${name}"]`);
            if (field) {
                field.value = value;
            }
        });

        const estimation = getEstimate();
        const estimationValue = estimation ? JSON.stringify(estimation) : JSON.stringify({ min: 0, max: 0, devise: 'FCFA' });
        const estimationField = form.querySelector('[name="estimation"]');
        if (estimationField) {
            estimationField.value = estimationValue;
        }

        const descriptionField = form.querySelector('[name="description_besoin"]');
        if (descriptionField) {
            descriptionField.value = buildDescription();
        }

        const nomField = form.querySelector('[name="nom"]');
        if (nomField) {
            nomField.value = state.nom;
        }

        const telephoneField = form.querySelector('[name="telephone"]');
        if (telephoneField) {
            telephoneField.value = state.telephone;
        }

        const villeField = form.querySelector('[name="ville"]');
        if (villeField) {
            villeField.value = state.ville;
        }

        const paysField = form.querySelector('[name="pays"]');
        if (paysField) {
            paysField.value = state.pays;
        }
    };

    const updateEstimatePanel = () => {
        const estimation = getEstimate();
        if (!estimation) {
            estimateRange.textContent = '—';
            estimateMeta.textContent = 'Complétez les étapes pour obtenir une fourchette indicative.';
            return;
        }

        estimateRange.textContent = `${formatMoney(estimation.min)} — ${formatMoney(estimation.max)}`;
        estimateMeta.textContent = 'Fourchette indicative. Le devis définitif est confirmé après visite technique.';
    };

    const updateChoiceStates = () => {
        panels.querySelectorAll('[data-finition-button], [data-vitrage-button]').forEach((button) => {
            const isSelected = button.dataset.finitionButton === state.finition || button.dataset.vitrageButton === state.vitrage;

            button.classList.toggle('border-amber-400', isSelected);
            button.classList.toggle('bg-amber-50', isSelected);
            button.classList.toggle('text-slate-900', isSelected);
            button.classList.toggle('border-stone-200', !isSelected);
            button.classList.toggle('bg-white', !isSelected);
            button.classList.toggle('text-slate-600', !isSelected);
            button.setAttribute('aria-pressed', String(isSelected));
        });

        panels.querySelectorAll('[data-option-toggle]').forEach((input) => {
            input.closest('label')?.classList.toggle('border-amber-400', input.checked);
            input.closest('label')?.classList.toggle('bg-amber-50', input.checked);
        });
    };

    const updateDimensionSummary = () => {
        const subtype = getSubtype();
        const quantityElement = panels.querySelector('[data-dimension-summary]');

        if (quantityElement) {
            quantityElement.textContent = getEstimatedQuantity() > 0
                ? `Quantité retenue : ${getEstimatedQuantity().toFixed(2)} ${subtype?.unit || ''}`
                : 'Saisissez des dimensions supérieures à zéro pour obtenir une estimation.';
            quantityElement.classList.toggle('text-amber-700', getEstimatedQuantity() > 0);
            quantityElement.classList.toggle('text-slate-500', getEstimatedQuantity() <= 0);
        }
    };

    const canAdvance = () => {
        if (state.step === 0) {
            return Boolean(state.categorie);
        }

        if (state.step === 1) {
            return Boolean(state.sousType);
        }

        if (state.step === 2) {
            return getEstimatedQuantity() > 0;
        }

        if (state.step === 3) {
            return true;
        }

        return Boolean(state.nom && state.telephone && state.ville && state.pays);
    };

    const updateNavigationControls = () => {
        nextStepButton.disabled = false;
        submitQuoteButton.disabled = false;
    };

    const showValidationMessage = () => {
        const messages = [
            'Sélectionnez une catégorie de projet.',
            'Sélectionnez un type de projet.',
            'Saisissez des dimensions supérieures à zéro.',
            '',
            'Renseignez votre nom, votre téléphone, votre ville et votre pays.',
        ];
        validationMessage.textContent = messages[state.step];
        validationMessage.classList.toggle('hidden', !messages[state.step]);

        const firstField = state.step === 2
            ? panels.querySelector('[data-dimension]')
            : state.step === 4
                ? panels.querySelector('input[name="nom"]')
                : null;
        firstField?.focus();
    };

    const renderStepper = () => {
        stepper.innerHTML = '';

        config.steps.forEach((label, index) => {
            const item = document.createElement('button');
            item.type = 'button';
            item.dataset.stepButton = String(index);
            item.className = `flex min-w-0 items-center justify-center gap-1 rounded-xl border px-1.5 py-2 text-center transition lg:w-full lg:justify-start lg:gap-3 lg:rounded-2xl lg:px-3 lg:text-left ${index === state.step ? 'border-amber-400 bg-amber-50 text-slate-900' : 'border-transparent bg-white text-slate-500 hover:border-stone-200 hover:bg-white'}`;
            item.innerHTML = `
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full border text-[11px] font-bold ${index <= state.step ? 'border-amber-400 bg-amber-100 text-amber-700' : 'border-stone-200 bg-white text-slate-400'}">
                    ${String(index + 1).padStart(2, '0')}
                </span>
                <span class="hidden text-sm font-medium lg:block">${label}</span>
            `;
            item.addEventListener('click', () => {
                if (index <= state.step || canAdvance()) {
                    state.step = index;
                    render();
                }
            });
            stepper.appendChild(item);
        });
    };

    const renderCategoryStep = () => {
        panels.innerHTML = `
            <section data-step-panel="0" class="rounded-3xl border border-stone-200 bg-stone-50 p-5">
                <h2 tabindex="-1" class="text-xl font-black text-slate-900">Quel type de projet ?</h2>
                <p class="mt-2 text-sm text-slate-600">Sélectionnez la catégorie qui correspond à votre besoin.</p>
                <div class="mt-5 grid gap-3 sm:grid-cols-2">
                    ${config.categories.map((category) => `
                        <button type="button" data-category-button="${category.id}" class="rounded-2xl border px-4 py-4 text-left transition ${state.categorie === category.id ? 'border-amber-400 bg-amber-50 shadow-sm' : 'border-stone-200 bg-white hover:border-stone-300'}">
                            <span class="block text-base font-bold text-slate-900">${category.label}</span>
                            <span class="mt-2 block text-sm text-slate-600">${category.description}</span>
                        </button>
                    `).join('')}
                </div>
            </section>
        `;

        panels.querySelectorAll('[data-category-button]').forEach((button) => {
            button.addEventListener('click', () => {
                state.categorie = button.dataset.categoryButton;
                state.sousType = '';
                state.step = 1;
                render();
            });
        });
    };

    const renderSubtypeStep = () => {
        const subtypeOptions = config.subtypes[state.categorie] || [];

        panels.innerHTML = `
            <section data-step-panel="1" class="rounded-3xl border border-stone-200 bg-stone-50 p-5">
                <h2 tabindex="-1" class="text-xl font-black text-slate-900">Précisez le type de projet</h2>
                <p class="mt-2 text-sm text-slate-600">${config.categories.find((category) => category.id === state.categorie)?.label || 'Projet'} • Choisissez le produit souhaité.</p>
                <div class="mt-5 grid gap-3 sm:grid-cols-2">
                    ${subtypeOptions.map((subtype) => `
                        <button type="button" data-subtype-button="${subtype.id}" class="rounded-2xl border px-4 py-4 text-left transition ${state.sousType === subtype.id ? 'border-amber-400 bg-amber-50 shadow-sm' : 'border-stone-200 bg-white hover:border-stone-300'}">
                            <span class="block text-base font-bold text-slate-900">${subtype.label}</span>
                            <span class="mt-1 block text-sm text-slate-600">À partir de ${formatMoney(subtype.base)} / ${subtype.unit}</span>
                        </button>
                    `).join('')}
                </div>
            </section>
        `;

        panels.querySelectorAll('[data-subtype-button]').forEach((button) => {
            button.addEventListener('click', () => {
                state.sousType = button.dataset.subtypeButton;
                state.step = 2;
                render();
            });
        });
    };

    const renderDimensionsStep = () => {
        const subtype = getSubtype();

        panels.innerHTML = `
            <section data-step-panel="2" class="rounded-3xl border border-stone-200 bg-stone-50 p-5">
                <h2 tabindex="-1" class="text-xl font-black text-slate-900">Dimensions</h2>
                <p class="mt-2 text-sm text-slate-600">${subtype ? subtype.label : 'Produit'} • Saisissez les dimensions du projet.</p>
                <div class="mt-5 ${subtype?.unit === 'm²' ? 'grid gap-4 sm:grid-cols-2' : 'max-w-md'}">
                    ${subtype?.unit === 'm²' ? `
                        <label for="dimension-largeur" class="flex w-full flex-col gap-2 text-sm font-medium text-slate-700">
                        <span>Largeur <span class="font-normal text-slate-400">(m)</span></span>
                        <input id="dimension-largeur" type="number" min="0" step="0.1" inputmode="decimal" autocomplete="off" data-dimension="largeur" value="${state.dimensions.largeur}" required class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-3.5 text-base text-slate-900 shadow-sm outline-none transition focus:border-amber-400 focus:ring-4 focus:ring-amber-100" placeholder="Ex. 1,20" />
                        </label>
                        <label for="dimension-hauteur" class="flex w-full flex-col gap-2 text-sm font-medium text-slate-700">
                        <span>Hauteur <span class="font-normal text-slate-400">(m)</span></span>
                        <input id="dimension-hauteur" type="number" min="0" step="0.1" inputmode="decimal" autocomplete="off" data-dimension="hauteur" value="${state.dimensions.hauteur}" required class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-3.5 text-base text-slate-900 shadow-sm outline-none transition focus:border-amber-400 focus:ring-4 focus:ring-amber-100" placeholder="Ex. 2,10" />
                        </label>
                    ` : `
                        <label for="dimension-longueur" class="flex w-full max-w-xs flex-col gap-2 text-sm font-medium text-slate-700">
                        <span>Longueur <span class="font-normal text-slate-400">(ml)</span></span>
                        <input id="dimension-longueur" type="number" min="0" step="0.1" inputmode="decimal" autocomplete="off" data-dimension="longueur" value="${state.dimensions.longueur}" required class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-3.5 text-base text-slate-900 shadow-sm outline-none transition focus:border-amber-400 focus:ring-4 focus:ring-amber-100" placeholder="Ex. 4,50" />
                        </label>
                    `}
                </div>
                <p data-dimension-summary class="mt-4 text-sm font-medium ${getEstimatedQuantity() > 0 ? 'text-amber-700' : 'text-slate-500'}">${getEstimatedQuantity() > 0 ? `Quantité retenue : ${getEstimatedQuantity().toFixed(2)} ${subtype?.unit || ''}` : 'Saisissez des dimensions supérieures à zéro pour obtenir une estimation.'}</p>
            </section>
        `;

        panels.querySelectorAll('[data-dimension]').forEach((input) => {
            input.addEventListener('input', (event) => {
                const field = event.target.dataset.dimension;
                state.dimensions[field] = event.target.value;
                syncFormValues();
                updateEstimatePanel();
                updateDimensionSummary();
                updateNavigationControls();
                validationMessage.classList.add('hidden');
            });
        });
    };

    const renderOptionsStep = () => {
        const subtype = getSubtype();

        panels.innerHTML = `
            <section data-step-panel="3" class="rounded-3xl border border-stone-200 bg-stone-50 p-5">
                <h2 tabindex="-1" class="text-xl font-black text-slate-900">Finitions & options</h2>
                <p class="mt-2 text-sm text-slate-600">Affinez la fourchette selon vos préférences.</p>

                <div class="mt-5">
                    <p class="mb-3 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Finition</p>
                    <div class="flex flex-wrap gap-2">
                        ${config.finitions.map((finition) => `
                            <button type="button" data-finition-button="${finition.id}" aria-pressed="${state.finition === finition.id}" class="min-h-11 rounded-full border px-4 py-2 text-sm font-semibold transition ${state.finition === finition.id ? 'border-amber-400 bg-amber-50 text-slate-900' : 'border-stone-200 bg-white text-slate-600 hover:border-stone-300'}">
                                ${finition.label}
                            </button>
                        `).join('')}
                    </div>
                </div>

                ${subtype?.hasGlazing ? `
                    <div class="mt-6">
                        <p class="mb-3 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Vitrage</p>
                        <div class="flex flex-wrap gap-2">
                            ${config.vitrages.map((vitrage) => `
                                <button type="button" data-vitrage-button="${vitrage.id}" aria-pressed="${state.vitrage === vitrage.id}" class="min-h-11 rounded-full border px-4 py-2 text-sm font-semibold transition ${state.vitrage === vitrage.id ? 'border-amber-400 bg-amber-50 text-slate-900' : 'border-stone-200 bg-white text-slate-600 hover:border-stone-300'}">
                                    ${vitrage.label}
                                </button>
                            `).join('')}
                        </div>
                    </div>
                ` : ''}

                <div class="mt-6">
                    <p class="mb-3 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Options</p>
                    <div class="space-y-2">
                        ${config.options.map((option) => `
                            <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-stone-200 bg-white px-3 py-3 text-sm text-slate-700 ${state.options.includes(option.id) ? 'border-amber-400 bg-amber-50' : ''}">
                                <input type="checkbox" data-option-toggle="${option.id}" ${state.options.includes(option.id) ? 'checked' : ''} class="h-5 w-5 accent-amber-500" />
                                <span>${option.label}</span>
                            </label>
                        `).join('')}
                    </div>
                </div>
            </section>
        `;

        panels.querySelectorAll('[data-finition-button]').forEach((button) => {
            button.addEventListener('click', () => {
                state.finition = button.dataset.finitionButton;
                syncFormValues();
                updateEstimatePanel();
                updateChoiceStates();
            });
        });

        panels.querySelectorAll('[data-vitrage-button]').forEach((button) => {
            button.addEventListener('click', () => {
                state.vitrage = button.dataset.vitrageButton;
                syncFormValues();
                updateEstimatePanel();
                updateChoiceStates();
            });
        });

        panels.querySelectorAll('[data-option-toggle]').forEach((input) => {
            input.addEventListener('change', (event) => {
                const optionId = event.target.dataset.optionToggle;
                const selected = new Set(state.options);

                if (event.target.checked) {
                    selected.add(optionId);
                } else {
                    selected.delete(optionId);
                }

                state.options = [...selected];
                syncFormValues();
                updateEstimatePanel();
                updateChoiceStates();
            });
        });
    };

    const renderContactStep = () => {
        panels.innerHTML = `
            <section data-step-panel="4" class="rounded-3xl border border-stone-200 bg-stone-50 p-5">
                <h2 tabindex="-1" class="text-xl font-black text-slate-900">Vos coordonnées</h2>
                <p class="mt-2 text-sm text-slate-600">Pour recevoir une réponse rapide et un devis détaillé.</p>
                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <label class="flex flex-col gap-2 text-sm font-medium text-slate-700 sm:col-span-2">
                        <span>Nom complet</span>
                        <input type="text" name="nom" value="${state.nom}" required class="rounded-xl border border-stone-200 bg-white px-3 py-2.5 text-slate-900 focus:border-amber-400 focus:outline-none" placeholder="Votre nom" />
                    </label>
                    <label class="flex flex-col gap-2 text-sm font-medium text-slate-700">
                        <span>Téléphone</span>
                        <input type="tel" name="telephone" value="${state.telephone}" required class="rounded-xl border border-stone-200 bg-white px-3 py-2.5 text-slate-900 focus:border-amber-400 focus:outline-none" placeholder="+228 ..." />
                    </label>
                    <label class="flex flex-col gap-2 text-sm font-medium text-slate-700">
                        <span>Ville</span>
                        <input type="text" name="ville" value="${state.ville}" required class="rounded-xl border border-stone-200 bg-white px-3 py-2.5 text-slate-900 focus:border-amber-400 focus:outline-none" placeholder="Lomé" />
                    </label>
                    <label class="flex flex-col gap-2 text-sm font-medium text-slate-700 sm:col-span-2">
                        <span>Pays</span>
                        <input type="text" name="pays" value="${state.pays}" required class="rounded-xl border border-stone-200 bg-white px-3 py-2.5 text-slate-900 focus:border-amber-400 focus:outline-none" placeholder="Togo" />
                    </label>
                </div>
            </section>
        `;

        panels.querySelectorAll('input[name="nom"]').forEach((input) => {
            input.addEventListener('input', (event) => {
                state.nom = event.target.value;
                syncFormValues();
                updateNavigationControls();
            });
        });

        panels.querySelectorAll('input[name="telephone"]').forEach((input) => {
            input.addEventListener('input', (event) => {
                state.telephone = event.target.value;
                syncFormValues();
                updateNavigationControls();
            });
        });

        panels.querySelectorAll('input[name="ville"]').forEach((input) => {
            input.addEventListener('input', (event) => {
                state.ville = event.target.value;
                syncFormValues();
                updateNavigationControls();
            });
        });

        panels.querySelectorAll('input[name="pays"]').forEach((input) => {
            input.addEventListener('input', (event) => {
                state.pays = event.target.value || 'Togo';
                syncFormValues();
                updateNavigationControls();
                validationMessage.classList.add('hidden');
            });
        });
    };

    const renderPanels = () => {
        const stepContentMap = {
            0: renderCategoryStep,
            1: renderSubtypeStep,
            2: renderDimensionsStep,
            3: renderOptionsStep,
            4: renderContactStep,
        };

        stepContentMap[state.step]?.();
    };

    const render = () => {
        renderStepper();
        renderPanels();
        syncFormValues();
        updateEstimatePanel();

        previousStepButton.classList.toggle('hidden', state.step === 0);
        nextStepButton.classList.toggle('hidden', state.step === 4);
        submitQuoteButton.classList.toggle('hidden', state.step !== 4);
        updateNavigationControls();
        previousStepButton.disabled = state.step === 0;
        nextStepButton.textContent = 'Suivant';
        requestAnimationFrame(() => panels.querySelector('[data-step-panel] h2')?.focus());
    };

    previousStepButton.addEventListener('click', () => {
        state.step = Math.max(0, state.step - 1);
        render();
    });

    nextStepButton.addEventListener('click', () => {
        if (!canAdvance()) {
            showValidationMessage();
            return;
        }

        state.step = Math.min(4, state.step + 1);
        render();
    });

    form.addEventListener('submit', (event) => {
        if (!canAdvance()) {
            event.preventDefault();
            showValidationMessage();
            return;
        }

        if (state.step !== 4) {
            event.preventDefault();
            state.step = 4;
            render();
            return;
        }

        syncFormValues();
        submitQuoteButton.disabled = true;
        submitQuoteButton.setAttribute('aria-busy', 'true');
        submitQuoteButton.textContent = 'Envoi en cours…';
    });

    render();
});
