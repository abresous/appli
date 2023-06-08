// start script autocomplete catégories 


        // Tableau de suggestions
        const suggestions = ["Ecran", "Cles USB", "Disque dur", "Cable RJ45", "RAM", "Souris", "Pack office", "Adobe creative cloud", "Licence", "Matériel", "Pc", "Serveur", "Clavier", "Switch", "Tapis de souris", "Articulate 360"];

        const input = document.getElementById("myInput");
        const autocompleteList = document.getElementById("autocomplete-list");

        input.addEventListener("input", function() {
            const value = this.value;
            autocompleteList.innerHTML = "";

            if (value.length > 0) {
                const matchedSuggestions = suggestions.filter(function(suggestion) {
                    return suggestion.toLowerCase().startsWith(value.toLowerCase());
                });

                matchedSuggestions.forEach(function(suggestion) {
                    const listItem = document.createElement("li");
                    listItem.textContent = suggestion;
                    listItem.addEventListener("click", function() {
                        input.value = suggestion;
                        autocompleteList.innerHTML = "";
                    });
                    autocompleteList.appendChild(listItem);
                });
            }
        });

        document.addEventListener("click", function(event) {
            if (!autocompleteList.contains(event.target)) {
                autocompleteList.innerHTML = "";
            }
        });

// end script autocomplete categories        