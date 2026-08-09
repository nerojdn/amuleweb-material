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

/* =========================================================
   MOBILE SORT
========================================================= */

document.addEventListener('DOMContentLoaded', function () {

  const sortButton = document.getElementById("mobileSortButton");
  const sortLabel = document.getElementById("mobileSortLabel");

  const sheet = document.getElementById("sortSheet");
  const backdrop = document.getElementById("sortSheetBackdrop");

  const closeButton = document.getElementById("sortSheetClose");

  if (!sortButton || !sheet || !backdrop) {
    return;
  }

  const sortOptions = document.querySelectorAll(".sort-option");

  const directionButtons = document.querySelectorAll(".sort-direction-btn");

  const params = new URLSearchParams(window.location.search);

  let currentSort = params.get("sort") || "name";

  let currentDirection = params.get("sortdir") || "asc";

  const labels = {
    name: "Nombre",
    size: "Tamaño",
    sources: "Fuentes",
  };

  function updateUI() {
    sortOptions.forEach((option) => {
      option.classList.toggle("active", option.dataset.sort === currentSort);
    });

    directionButtons.forEach((button) => {
      button.classList.toggle("active", button.dataset.direction === currentDirection);
    });

    if (labels[currentSort]) {
      const arrow = currentDirection === "asc" ? "↑" : "↓";

      sortLabel.textContent = labels[currentSort] + " " + arrow;
    }
  }

  function openSheet() {
    updateUI();

    backdrop.style.display = "block";

    requestAnimationFrame(() => {
      backdrop.style.opacity = "1";
      sheet.style.transform = "translateY(0)";
    });
  }

  function closeSheet() {
    backdrop.style.opacity = "0";
    sheet.style.transform = "translateY(100%)";

    setTimeout(() => {
      backdrop.style.display = "none";
    }, 250);
  }

  function applySort() {
    const url = new URL("amuleweb-main-search.php", window.location.href);

    url.searchParams.set("sort", currentSort);

    url.searchParams.set("sortdir", currentDirection);

    window.location.href = url.toString();
  }

  sortButton.addEventListener("click", openSheet);

  closeButton.addEventListener("click", closeSheet);

  backdrop.addEventListener("click", closeSheet);

  sortOptions.forEach((option) => {
    option.addEventListener("click", function (event) {
      event.preventDefault();

      currentSort = this.dataset.sort;

      updateUI();

      /*
       * No aplicamos todavía.
       * Primero permitimos elegir dirección.
       */
    });
  });

  directionButtons.forEach((button) => {
    button.addEventListener("click", function () {
      currentDirection = this.dataset.direction;

      updateUI();

      applySort();
    });
  });

  updateUI();

});
