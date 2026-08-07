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
