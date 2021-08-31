function groupSelect() {
  /* get the section */
  let groupSelect = document.querySelector(".group-select");

  /* check if the section have hidden class */
  let groupHidden = groupSelect.classList.contains("hidden");

  let userSelect = document.querySelector(".user-select");
  let userHidden = userSelect.classList.contains("hidden");

  let quizSelect = document.querySelector(".quiz-select");
  let quizHidden = quizSelect.classList.contains("hidden");

  /*Check what is hidden and what is not to hidden wich one necessary */
  if (quizHidden) {
    if (userHidden) {
      groupSelect.classList.toggle("hidden");
    } else {
      userSelect.classList.toggle("hidden");
      groupSelect.classList.toggle("hidden");
    }
  } else {
    if (userHidden) {
      quizSelect.classList.toggle("hidden");
      groupSelect.classList.toggle("hidden");
    } else {
      quizSelect.classList.toggle("hidden");
      userSelect.classList.toggle("hidden");
      groupSelect.classList.toggle("hidden");
    }
  }
}

function quizSelect() {
  let groupSelect = document.querySelector(".group-select");
  let groupHidden = groupSelect.classList.contains("hidden");

  let userSelect = document.querySelector(".user-select");
  let userHidden = userSelect.classList.contains("hidden");

  let quizSelect = document.querySelector(".quiz-select");
  let quizHidden = quizSelect.classList.contains("hidden");

  if (groupHidden) {
    if (userHidden) {
      quizSelect.classList.toggle("hidden");
    } else {
      userSelect.classList.toggle("hidden");
      quizSelect.classList.toggle("hidden");
    }
  } else {
    if (userHidden) {
      quizSelect.classList.toggle("hidden");
      groupSelect.classList.toggle("hidden");
    } else {
      quizSelect.classList.toggle("hidden");
      userSelect.classList.toggle("hidden");
      groupSelect.classList.toggle("hidden");
    }
  }
}

function userSelect() {
  let groupSelect = document.querySelector(".group-select");
  let groupHidden = groupSelect.classList.contains("hidden");

  let userSelect = document.querySelector(".user-select");
  let userHidden = userSelect.classList.contains("hidden");

  let quizSelect = document.querySelector(".quiz-select");
  let quizHidden = quizSelect.classList.contains("hidden");

  if (quizHidden) {
    if (groupHidden) {
      userSelect.classList.toggle("hidden");
    } else {
      userSelect.classList.toggle("hidden");
      groupSelect.classList.toggle("hidden");
    }
  } else {
    if (groupHidden) {
      quizSelect.classList.toggle("hidden");
      userSelect.classList.toggle("hidden");
    } else {
      quizSelect.classList.toggle("hidden");
      userSelect.classList.toggle("hidden");
      groupSelect.classList.toggle("hidden");
    }
  }
}

function quizStorage() {
  sessionStorage.setItem("quiz", "true");
}

function userStorage() {
  sessionStorage.setItem("user", "true");
}

function groupStorage() {
  sessionStorage.setItem("group", "true");
}

function postRequest() {
  var quiz = sessionStorage.getItem("quiz");

  if (sessionStorage.getItem("quiz") != null) {
    let quizSelect = document.querySelector(".quiz-select");
    let quizHidden = quizSelect.classList.contains("hidden");

    if (quizHidden) {
      quizSelect.classList.toggle("hidden");
    }
  }

  if (sessionStorage.getItem("user") != null) {
    let userSelect = document.querySelector(".user-select");
    let userHidden = userSelect.classList.contains("hidden");

    if (userHidden) {
      userSelect.classList.toggle("hidden");
    }
  }

  if (sessionStorage.getItem("group") != null) {
    let groupSelect = document.querySelector(".group-select");
    let groupHidden = groupSelect.classList.contains("hidden");

    if (groupHidden) {
      groupSelect.classList.toggle("hidden");
    }
  }

  sessionStorage.clear();
}

/*create the groups variable to stock all group taxonomy terms*/
let groups = [];

/**
 * function to autocomplete the search
 * @param  {[string]} inp               input comming from the search form
 */
function groupAutocomplete(input) {
  /* inputValue stock the typed value  */
  let inputValue = input.value;
  /* nb of letter type */
  let nbLetters = inputValue.length;

  /* loop on the table to create the list - if already exist don't make a duplicate */
  groups.forEach(function (group) {
    /* we search for each value in table group if the inputvalue is the same */
    let regex = new RegExp(`^${inputValue}`);

    if (regex.test(group.substring(0, nbLetters))) {
      if (!document.getElementById(group)) {
        let suggestions = document.createElement("div");
        suggestions.setAttribute("id", group);
        suggestions.setAttribute("class", "autocomplete-items");
        suggestions.innerHTML = group;
        document.querySelector(".autocomplete").append(suggestions);
        console.log("correspondance trouvée");
        console.log(regex);
      } else {
        console.log("pas de correspondance");
      }
    }

    /*for (let i = 0; i < groups.length; i++) {
      if (
        groups[i].substr(0, nbLetters).toUpperCase() == inputValue.toUpperCase()
      ) {
        let b = document.createElement("DIV");
        b.innerHTML = "<strong>" + groups[i].substr(0, nbLetters) + "</strong>";
      }
      document.querySelector(".autocomplete").appendChild(b);
    }*/
  });
}

window.onload = function () {
  postRequest();
};

/**
 * Funciton to get all taxonomy group term from twig - json_encode in twig, parse with this function and impletement into the groups varibale
 * @type {[type]}
 */
document.addEventListener("DOMContentLoaded", () => {
  // Select elements by their data attribute
  const groupsElements = document.querySelectorAll("[data-entry-groups]");

  // Map over each element and extract the data value
  const groupsInfoObjects = Array.from(groupsElements).map((item) =>
    JSON.parse(item.dataset.entryGroups)
  );

  // You'll now have an array of objects to work with
  console.log(groupsInfoObjects);

  /*loop on each entry to get the usergroup */
  groupsInfoObjects.forEach(function (entry) {
    for (let i = 0; i < groupsInfoObjects[0].length; i++) {
      /*implement the group taxonomy terms in the variable groups to be used into my autocompletion bar search*/
      groups.push(entry[i].usergroup);
      console.log(groups);
    }
  });
});
