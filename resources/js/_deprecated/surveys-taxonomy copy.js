export function choicesListeners(SearchURL, StoreOrUpdateURL, choicesSelectorClass) {
    if( SearchURL && StoreOrUpdateURL && choicesSelectorClass){
        // https://github.com/Choices-js/Choices#callbackoncreatetemplates

        // Initialize Choices for each select element with the class 'term-choice'
        var choicesSelector = document.querySelectorAll(choicesSelectorClass);
        if(choicesSelector){
            choicesSelector.forEach(function(select) {

                var selectId = select.id;
                //console.log('selectId: ', selectId);

                var isChoiceEl = document.getElementById(selectId);

                var choicesInstance = new Choices(isChoiceEl, {
                    removeItems: true,
                    removeItemButton: true,
                    noResultsText: 'Nenhum resultado encontrado',
                    noChoicesText: 'Sem opções para escolher',
                    itemSelectText: 'Pressione para selecionar',
                    allowHTML: true,
                    addItems: true,
                    editItems: false,
                    duplicateItemsAllowed: false,
                    addItemText: (value) => {
                        return `Pressione Enter para adicionar <b>"${value}"</b>`;
                    },
                    maxItemText: (maxItemCount) => {
                        return `Apenas ${maxItemCount} das opções pode(m) ser selecionada(s)`;
                    }
                });
                //console.log(choicesInstance);

                // Function to fetch data and update Choices.js for the specific select element
                function fetchAndPopulateData(query, currentChoicesInstance) {
                    fetch(SearchURL + `?query=${encodeURIComponent(query)}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            //console.log('Data search fetched:', data);
                            //console.log('Current Choices Instance:', currentChoicesInstance);

                            // Clear existing choices before setting new ones if needed
                            currentChoicesInstance.clearChoices();

                            // Assuming 'data' is an array of terms
                            currentChoicesInstance.setChoices(data.map(term => ({
                                value: term.term_id,
                                label: term.name,
                                selected: false,
                                disabled: false,
                            })), 'value', 'label', false);
                        })
                        .catch(error => {
                            console.error('Error fetching terms:', error);
                        });
                }

                // Listen for when the user types and fetch data for the specific select element
                isChoiceEl.addEventListener(
                    'search', function(event) {
                        if (event.detail.value) {
                            fetchAndPopulateData(event.detail.value, choicesInstance);
                        }
                    }
                );

                // Listen for when a new item is added (if you allow adding new terms)
                choicesInstance.passedElement.element.addEventListener(
                    'addItem', function(event) {
                        console.log('addItem event:', event);

                        if (event.detail.value) {
                            // Logic to add a new term to database
                            // and then fetch and populate data again if needed
                            var newTerm = { name: event.detail.value };
                            fetch(StoreOrUpdateURL, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify(newTerm)
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Network response was not ok');
                                }
                                return response.json();
                            })
                            .then(data => {
                                console.log('Data addItem fetched:', data);

                                if(data.success) {
                                    // Add the new term to the choices
                                    choicesInstance.setChoices([{
                                        value: data.term.term_id,
                                        label: data.term.name,
                                        selected: true,
                                        disabled: false,
                                    }], 'value', 'label', false);
                                }
                            })
                            .catch(error => {
                                console.error('Error during fetch operation:', error);
                            });
                        }

                        return;
                    }
                );

            });
        }
    }
}

