function loadComponent(url, target) {
  fetch(url)
    .then(function (response) {
      return response.text();
    })

    .then(function (html) {
      document.getElementById(target).innerHTML = html;
      highlightNavigation();
    })

    .catch(function () {
      console.log("Error cargando componente: " + url);
    });
}

function highlightNavigation() {
  var current = window.location.pathname.split("/").pop();

  document
    .querySelectorAll(".sidebar a, .bottom-nav a")
    .forEach(function (link) {
      if (link.getAttribute("href") === current) {
        link.classList.add("active");
      }
    });
}

function updateSelectionBar() {
  const cards = document.querySelectorAll(".card");

  let selectedCount = 0;

  cards.forEach((card) => {
    const checkbox = card.querySelector('input[type="checkbox"]');

    if (!checkbox) {
      return;
    }

    if (checkbox.checked) {
      selectedCount++;

      card.classList.add("selected");
    } else {
      card.classList.remove("selected");
    }
  });

  const floatingActions = document.getElementById("floatingActions");

  const selectedCountElement = document.getElementById("selectedCount");

  if (!floatingActions) {
    return;
  }

  if (selectedCount > 0) {
    floatingActions.classList.add("visible");

    if (selectedCountElement) {
      selectedCountElement.textContent = "Descargar (" + selectedCount + ")";
    }
  } else {
    floatingActions.classList.remove("visible");
  }
}
