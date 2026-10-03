//
const countrySelect = document.getElementById("country_id");
const stateSelect = document.getElementById("state_id");
const districtSelect = document.getElementById("district_id");

countrySelect && countrySelect.addEventListener("change", function(e){
    const url = countrySelect.getAttribute("data-url");

    fetch(url + "/?country_id=" + e.target.value, {
        "headers": {
            "Accept": "application/json",
            "X-Requested-With": "XMLHttpRequest",
        }
    })
        .then(res => res.json())
        .then(data => {
            const perviousStateOptions = [...stateSelect.querySelectorAll("option")];

            for(let i = 0; i < perviousStateOptions.length; i++){
                stateSelect.removeChild(perviousStateOptions[i]);
            }

            for(let i = 0; i < data.data.length; i++){
                const StateOption = document.createElement("option");
                StateOption.value = data.data[i].id;
                StateOption.innerText = data.data[i].name;
                stateSelect.appendChild(StateOption);
            }

        })
        .catch(err => console.log(err))
});

stateSelect && stateSelect.addEventListener("change", function(e){
    const url = stateSelect.getAttribute("data-url");
    fetch(url + "/?state_id=" + e.target.value, {
        "headers": {
            "Accept": "application/json",
            "X-Requested-With": "XMLHttpRequest",
        }
    })
        .then(res => res.json())
        .then(data => {
            const perviousDistrictOptions = [...districtSelect.querySelectorAll("option")];

            for(let i = 0; i < perviousDistrictOptions.length; i++){
                districtSelect.removeChild(perviousDistrictOptions[i]);
            }

            for(let i = 0; i < data.data.length; i++){
                const districtOption = document.createElement("option");
                districtOption.value = data.data[i].id;
                districtOption.innerText = data.data[i].name;
                districtSelect.appendChild(districtOption);
            }
        })
        .catch(err => console.log(err))
});

