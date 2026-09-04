document.addEventListener(`DOMContentLoaded`,()=>{let e=document.getElementById(`mobile-menu-toggle`),t=document.getElementById(`mobile-menu`);if(e&&t){let n=n=>{e.setAttribute(`aria-expanded`,String(n)),e.setAttribute(`aria-label`,n?`Fermer le menu`:`Ouvrir le menu`),e.classList.toggle(`active`,n),t.classList.toggle(`hidden`,!n)};e.addEventListener(`click`,()=>{let t=e.getAttribute(`aria-expanded`)===`true`;n(!t)}),t.querySelectorAll(`a`).forEach(e=>{e.addEventListener(`click`,()=>n(!1))}),document.addEventListener(`keydown`,t=>{t.key===`Escape`&&e.getAttribute(`aria-expanded`)===`true`&&(n(!1),e.focus())})}let n=document.getElementById(`quote-config`);if(!n)return;let r;try{r=JSON.parse(n.textContent||`{}`)}catch{n.insertAdjacentHTML(`afterend`,`<p role="alert" class="mt-4 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">Le configurateur est temporairement indisponible. Veuillez réessayer dans quelques instants.</p>`);return}let i=r.config??r,a=r.old??{},o=document.getElementById(`quote-configurator-form`),s=document.getElementById(`stepper`),c=document.getElementById(`step-panels`),l=document.getElementById(`previous-step`),u=document.getElementById(`next-step`),d=document.getElementById(`submit-quote`),f=document.getElementById(`estimate-range`),p=document.getElementById(`estimate-meta`),m=document.getElementById(`quote-validation-message`);if(!o||!s||!c||!l||!u||!d||!f||!p||!m)return;let h={step:0,categorie:o.querySelector(`[name="categorie"]`)?.value||``,sousType:o.querySelector(`[name="sous_type"]`)?.value||``,dimensions:(()=>{let e=o.querySelector(`[name="dimensions"]`)?.value;try{return JSON.parse(e||`{"largeur":"","hauteur":"","longueur":""}`)}catch{return{largeur:``,hauteur:``,longueur:``}}})(),finition:o.querySelector(`[name="finition"]`)?.value||i.finitions?.[1]?.id||`ral`,vitrage:o.querySelector(`[name="vitrage"]`)?.value||i.vitrages?.[1]?.id||`double`,options:(()=>{let e=o.querySelector(`[name="options"]`)?.value||`[]`;try{return JSON.parse(e)}catch{return[]}})(),nom:a.nom||``,telephone:a.telephone||``,ville:a.ville||``,pays:a.pays||`Togo`},g=new Intl.NumberFormat(`fr-FR`,{minimumFractionDigits:0,maximumFractionDigits:0}),_=e=>`${g.format(Math.round(e))} FCFA`,v=e=>{let t=Number.parseFloat(String(e).replace(`,`,`.`));return Number.isFinite(t)?t:0},y=()=>!h.categorie||!i.subtypes?.[h.categorie]?null:i.subtypes[h.categorie].find(e=>e.id===h.sousType)||null,b=()=>{let e=y();return e?e.unit===`m²`?v(h.dimensions.largeur)*v(h.dimensions.hauteur):v(h.dimensions.longueur):0},x=()=>{let e=y(),t=b();if(!e||t<=0)return null;let n=i.finitions.find(e=>e.id===h.finition)||i.finitions[0],r=e.hasGlazing?i.vitrages.find(e=>e.id===h.vitrage)||i.vitrages[0]:{multiplier:1},a=e.base*t*(n?.multiplier??1)*(r?.multiplier??1);return h.options.forEach(e=>{let n=i.options.find(t=>t.id===e);n&&(n.type===`flat`&&(a+=Number(n.value||0)),n.type===`perUnit`&&(a+=Number(n.value||0)*t),n.type===`percent`&&(a+=a*Number(n.value||0)))}),{min:Math.round(a*.85),max:Math.round(a*1.15),devise:`FCFA`}},S=()=>{let e=y(),t=i.categories.find(e=>e.id===h.categorie),n=[];if(t&&n.push(`Catégorie : ${t.label}`),e&&n.push(`Produit : ${e.label}`),b()>0){let t=e?.unit===`m²`?`${h.dimensions.largeur||0} x ${h.dimensions.hauteur||0} m`:`${h.dimensions.longueur||0} ml`;n.push(`Dimensions : ${t}`)}if(h.finition){let e=i.finitions.find(e=>e.id===h.finition);e&&n.push(`Finition : ${e.label}`)}if(e?.hasGlazing&&h.vitrage){let e=i.vitrages.find(e=>e.id===h.vitrage);e&&n.push(`Vitrage : ${e.label}`)}if(h.options.length>0){let e=h.options.map(e=>i.options.find(t=>t.id===e)?.label).filter(Boolean);e.length>0&&n.push(`Options : ${e.join(`, `)}`)}return n.length>0?n.join(` — `):`Demande de devis`},C=()=>{let e={categorie:h.categorie,sous_type:h.sousType,dimensions:JSON.stringify(h.dimensions),finition:h.finition,vitrage:h.vitrage,options:JSON.stringify(h.options),source:`simulateur`};Object.entries(e).forEach(([e,t])=>{let n=o.querySelector(`[name="${e}"]`);n&&(n.value=t)});let t=x(),n=JSON.stringify(t||{min:0,max:0,devise:`FCFA`}),r=o.querySelector(`[name="estimation"]`);r&&(r.value=n);let i=o.querySelector(`[name="description_besoin"]`);i&&(i.value=S());let a=o.querySelector(`[name="nom"]`);a&&(a.value=h.nom);let s=o.querySelector(`[name="telephone"]`);s&&(s.value=h.telephone);let c=o.querySelector(`[name="ville"]`);c&&(c.value=h.ville);let l=o.querySelector(`[name="pays"]`);l&&(l.value=h.pays)},w=()=>{let e=x();if(!e){f.textContent=`—`,p.textContent=`Complétez les étapes pour obtenir une fourchette indicative.`;return}f.textContent=`${_(e.min)} — ${_(e.max)}`,p.textContent=`Fourchette indicative. Le devis définitif est confirmé après visite technique.`},T=()=>h.step===0?!!h.categorie:h.step===1?!!h.sousType:h.step===2?b()>0:h.step===3||!!(h.nom&&h.telephone&&h.ville&&h.pays),E=()=>{u.disabled=!1,d.disabled=!1},D=()=>{let e=[`Sélectionnez une catégorie de projet.`,`Sélectionnez un type de projet.`,`Saisissez des dimensions supérieures à zéro.`,``,`Renseignez votre nom, votre téléphone, votre ville et votre pays.`];m.textContent=e[h.step],m.classList.toggle(`hidden`,!e[h.step]),(h.step===2?c.querySelector(`[data-dimension]`):h.step===4?c.querySelector(`input[name="nom"]`):null)?.focus()},O=()=>{s.innerHTML=``,i.steps.forEach((e,t)=>{let n=document.createElement(`button`);n.type=`button`,n.dataset.stepButton=String(t),n.className=`flex min-w-0 items-center justify-center gap-1 rounded-xl border px-1.5 py-2 text-center transition lg:w-full lg:justify-start lg:gap-3 lg:rounded-2xl lg:px-3 lg:text-left ${t===h.step?`border-amber-400 bg-amber-50 text-slate-900`:`border-transparent bg-white text-slate-500 hover:border-stone-200 hover:bg-white`}`,n.innerHTML=`
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full border text-[11px] font-bold ${t<=h.step?`border-amber-400 bg-amber-100 text-amber-700`:`border-stone-200 bg-white text-slate-400`}">
                    ${String(t+1).padStart(2,`0`)}
                </span>
                <span class="hidden text-sm font-medium lg:block">${e}</span>
            `,n.addEventListener(`click`,()=>{(t<=h.step||T())&&(h.step=t,F())}),s.appendChild(n)})},k=()=>{c.innerHTML=`
            <section data-step-panel="0" class="rounded-3xl border border-stone-200 bg-stone-50 p-5">
                <h2 tabindex="-1" class="text-xl font-black text-slate-900">Quel type de projet ?</h2>
                <p class="mt-2 text-sm text-slate-600">Sélectionnez la catégorie qui correspond à votre besoin.</p>
                <div class="mt-5 grid gap-3 sm:grid-cols-2">
                    ${i.categories.map(e=>`
                        <button type="button" data-category-button="${e.id}" class="rounded-2xl border px-4 py-4 text-left transition ${h.categorie===e.id?`border-amber-400 bg-amber-50 shadow-sm`:`border-stone-200 bg-white hover:border-stone-300`}">
                            <span class="block text-base font-bold text-slate-900">${e.label}</span>
                            <span class="mt-2 block text-sm text-slate-600">${e.description}</span>
                        </button>
                    `).join(``)}
                </div>
            </section>
        `,c.querySelectorAll(`[data-category-button]`).forEach(e=>{e.addEventListener(`click`,()=>{h.categorie=e.dataset.categoryButton,h.sousType=``,h.step=1,F()})})},A=()=>{let e=i.subtypes[h.categorie]||[];c.innerHTML=`
            <section data-step-panel="1" class="rounded-3xl border border-stone-200 bg-stone-50 p-5">
                <h2 tabindex="-1" class="text-xl font-black text-slate-900">Précisez le type de projet</h2>
                <p class="mt-2 text-sm text-slate-600">${i.categories.find(e=>e.id===h.categorie)?.label||`Projet`} • Choisissez le produit souhaité.</p>
                <div class="mt-5 grid gap-3 sm:grid-cols-2">
                    ${e.map(e=>`
                        <button type="button" data-subtype-button="${e.id}" class="rounded-2xl border px-4 py-4 text-left transition ${h.sousType===e.id?`border-amber-400 bg-amber-50 shadow-sm`:`border-stone-200 bg-white hover:border-stone-300`}">
                            <span class="block text-base font-bold text-slate-900">${e.label}</span>
                            <span class="mt-1 block text-sm text-slate-600">À partir de ${_(e.base)} / ${e.unit}</span>
                        </button>
                    `).join(``)}
                </div>
            </section>
        `,c.querySelectorAll(`[data-subtype-button]`).forEach(e=>{e.addEventListener(`click`,()=>{h.sousType=e.dataset.subtypeButton,h.step=2,F()})})},j=()=>{let e=y();c.innerHTML=`
            <section data-step-panel="2" class="rounded-3xl border border-stone-200 bg-stone-50 p-5">
                <h2 tabindex="-1" class="text-xl font-black text-slate-900">Dimensions</h2>
                <p class="mt-2 text-sm text-slate-600">${e?e.label:`Produit`} • Saisissez les dimensions du projet.</p>
                <div class="mt-5 ${e?.unit===`m²`?`grid gap-4 sm:grid-cols-2`:`max-w-md`}">
                    ${e?.unit===`m²`?`
                        <label for="dimension-largeur" class="flex w-full flex-col gap-2 text-sm font-medium text-slate-700">
                            <span>Largeur (m)</span>
                            <input id="dimension-largeur" type="number" min="0" step="0.1" inputmode="decimal" data-dimension="largeur" value="${h.dimensions.largeur}" required class="w-full rounded-xl border border-stone-200 bg-white px-3 py-3 text-slate-900 focus:border-amber-400 focus:outline-none" />
                        </label>
                        <label for="dimension-hauteur" class="flex w-full flex-col gap-2 text-sm font-medium text-slate-700">
                            <span>Hauteur (m)</span>
                            <input id="dimension-hauteur" type="number" min="0" step="0.1" inputmode="decimal" data-dimension="hauteur" value="${h.dimensions.hauteur}" required class="w-full rounded-xl border border-stone-200 bg-white px-3 py-3 text-slate-900 focus:border-amber-400 focus:outline-none" />
                        </label>
                    `:`
                        <label for="dimension-longueur" class="flex w-full max-w-xs flex-col gap-2 text-sm font-medium text-slate-700">
                            <span>Longueur (ml)</span>
                            <input id="dimension-longueur" type="number" min="0" step="0.1" inputmode="decimal" data-dimension="longueur" value="${h.dimensions.longueur}" required class="w-full rounded-xl border border-stone-200 bg-white px-3 py-3 text-slate-900 focus:border-amber-400 focus:outline-none" />
                        </label>
                    `}
                </div>
                ${b()>0?`<p class="mt-4 text-sm font-medium text-amber-700">Quantité retenue : ${b().toFixed(2)} ${e?.unit||``}</p>`:``}
            </section>
        `,c.querySelectorAll(`[data-dimension]`).forEach(e=>{e.addEventListener(`input`,e=>{let t=e.target.dataset.dimension;h.dimensions[t]=e.target.value,C(),w(),E(),m.classList.add(`hidden`)})})},M=()=>{let e=y();c.innerHTML=`
            <section data-step-panel="3" class="rounded-3xl border border-stone-200 bg-stone-50 p-5">
                <h2 tabindex="-1" class="text-xl font-black text-slate-900">Finitions & options</h2>
                <p class="mt-2 text-sm text-slate-600">Affinez la fourchette selon vos préférences.</p>

                <div class="mt-5">
                    <p class="mb-3 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Finition</p>
                    <div class="flex flex-wrap gap-2">
                        ${i.finitions.map(e=>`
                            <button type="button" data-finition-button="${e.id}" class="rounded-full border px-3 py-2 text-sm font-medium transition ${h.finition===e.id?`border-amber-400 bg-amber-50 text-slate-900`:`border-stone-200 bg-white text-slate-600 hover:border-stone-300`}">
                                ${e.label}
                            </button>
                        `).join(``)}
                    </div>
                </div>

                ${e?.hasGlazing?`
                    <div class="mt-6">
                        <p class="mb-3 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Vitrage</p>
                        <div class="flex flex-wrap gap-2">
                            ${i.vitrages.map(e=>`
                                <button type="button" data-vitrage-button="${e.id}" class="rounded-full border px-3 py-2 text-sm font-medium transition ${h.vitrage===e.id?`border-amber-400 bg-amber-50 text-slate-900`:`border-stone-200 bg-white text-slate-600 hover:border-stone-300`}">
                                    ${e.label}
                                </button>
                            `).join(``)}
                        </div>
                    </div>
                `:``}

                <div class="mt-6">
                    <p class="mb-3 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Options</p>
                    <div class="space-y-2">
                        ${i.options.map(e=>`
                            <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-stone-200 bg-white px-3 py-3 text-sm text-slate-700 ${h.options.includes(e.id)?`border-amber-400 bg-amber-50`:``}">
                                <input type="checkbox" data-option-toggle="${e.id}" ${h.options.includes(e.id)?`checked`:``} class="h-4 w-4 accent-amber-500" />
                                <span>${e.label}</span>
                            </label>
                        `).join(``)}
                    </div>
                </div>
            </section>
        `,c.querySelectorAll(`[data-finition-button]`).forEach(e=>{e.addEventListener(`click`,()=>{h.finition=e.dataset.finitionButton,C(),F()})}),c.querySelectorAll(`[data-vitrage-button]`).forEach(e=>{e.addEventListener(`click`,()=>{h.vitrage=e.dataset.vitrageButton,C(),F()})}),c.querySelectorAll(`[data-option-toggle]`).forEach(e=>{e.addEventListener(`change`,e=>{let t=e.target.dataset.optionToggle,n=new Set(h.options);e.target.checked?n.add(t):n.delete(t),h.options=[...n],C(),F()})})},N=()=>{c.innerHTML=`
            <section data-step-panel="4" class="rounded-3xl border border-stone-200 bg-stone-50 p-5">
                <h2 tabindex="-1" class="text-xl font-black text-slate-900">Vos coordonnées</h2>
                <p class="mt-2 text-sm text-slate-600">Pour recevoir une réponse rapide et un devis détaillé.</p>
                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <label class="flex flex-col gap-2 text-sm font-medium text-slate-700 sm:col-span-2">
                        <span>Nom complet</span>
                        <input type="text" name="nom" value="${h.nom}" required class="rounded-xl border border-stone-200 bg-white px-3 py-2.5 text-slate-900 focus:border-amber-400 focus:outline-none" placeholder="Votre nom" />
                    </label>
                    <label class="flex flex-col gap-2 text-sm font-medium text-slate-700">
                        <span>Téléphone</span>
                        <input type="tel" name="telephone" value="${h.telephone}" required class="rounded-xl border border-stone-200 bg-white px-3 py-2.5 text-slate-900 focus:border-amber-400 focus:outline-none" placeholder="+228 ..." />
                    </label>
                    <label class="flex flex-col gap-2 text-sm font-medium text-slate-700">
                        <span>Ville</span>
                        <input type="text" name="ville" value="${h.ville}" required class="rounded-xl border border-stone-200 bg-white px-3 py-2.5 text-slate-900 focus:border-amber-400 focus:outline-none" placeholder="Lomé" />
                    </label>
                    <label class="flex flex-col gap-2 text-sm font-medium text-slate-700 sm:col-span-2">
                        <span>Pays</span>
                        <input type="text" name="pays" value="${h.pays}" required class="rounded-xl border border-stone-200 bg-white px-3 py-2.5 text-slate-900 focus:border-amber-400 focus:outline-none" placeholder="Togo" />
                    </label>
                </div>
            </section>
        `,c.querySelectorAll(`input[name="nom"]`).forEach(e=>{e.addEventListener(`input`,e=>{h.nom=e.target.value,C(),E()})}),c.querySelectorAll(`input[name="telephone"]`).forEach(e=>{e.addEventListener(`input`,e=>{h.telephone=e.target.value,C(),E()})}),c.querySelectorAll(`input[name="ville"]`).forEach(e=>{e.addEventListener(`input`,e=>{h.ville=e.target.value,C(),E()})}),c.querySelectorAll(`input[name="pays"]`).forEach(e=>{e.addEventListener(`input`,e=>{h.pays=e.target.value||`Togo`,C(),E(),m.classList.add(`hidden`)})})},P=()=>{({0:k,1:A,2:j,3:M,4:N})[h.step]?.()},F=()=>{O(),P(),C(),w(),l.classList.toggle(`hidden`,h.step===0),u.classList.toggle(`hidden`,h.step===4),d.classList.toggle(`hidden`,h.step!==4),E(),l.disabled=h.step===0,u.textContent=`Suivant`,requestAnimationFrame(()=>c.querySelector(`[data-step-panel] h2`)?.focus())};l.addEventListener(`click`,()=>{h.step=Math.max(0,h.step-1),F()}),u.addEventListener(`click`,()=>{if(!T()){D();return}h.step=Math.min(4,h.step+1),F()}),o.addEventListener(`submit`,e=>{if(!T()){e.preventDefault(),D();return}if(h.step!==4){e.preventDefault(),h.step=4,F();return}C(),d.disabled=!0,d.setAttribute(`aria-busy`,`true`),d.textContent=`Envoi en cours…`}),F()});