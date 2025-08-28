document.addEventListener("DOMContentLoaded", function () {
  const sinVacantes = document.getElementById("sinVacantes");
  const contadorVacantes = document.getElementById("contadorVacantes");

  const jobList = document.querySelector(".job-list");
  const popup_login = document.getElementById("popup-login");
  const close_login = document.getElementById("close-login");

  if (close_login) {
    close_login.addEventListener("click", function () {
      popup_login.style.display = "none";
    });
  }

  let favorites = [];
  // Verificar si la página actual es la de favoritos
  if (jobList) {
    if (favs_query_vars.isUserLoggedIn) {
      favorites = getUserFavs();
      const listItems = jobList.querySelectorAll("li"); // Obtener todos los <li> dentro del UL
      if (listItems.length > 0) {
        listItems.forEach((li) => {
          const jobId = li.dataset.id; // Obtener el valor de data-id del <li>
          const isFavorite = favorites.some((fav) => fav.job_id === jobId);
          if (isFavorite) {
            li.classList.remove("add-fav"); // Eliminar la clase add-fav
            li.classList.add("remove-fav"); // Agregar la clase remove-fav
          } else {
            li.classList.remove("remove-fav"); // Asegurarse de eliminar remove-fav
            li.classList.add("add-fav"); // Agregar la clase add-fav
          }
        });
      } else {
        if (favorites.length == 0) {
          contadorVacantes.style.display = "none";
        }
      }
    }
  }

  function getUserFavs() {
    let favorites = [];

    jQuery.ajax({
      url: favs_query_vars.ajax_url,
      type: "POST",
      data: {
        action: "get_user_favorites",
      },
      async: false, // Solicitud síncrona
      success: function (response) {
        if (response.success) {
          favorites = response.data.favorites;
        } else {
          console.error(
            "Error al obtener los favoritos:",
            response.data.message
          );
        }
      },
      error: function (xhr, status, error) {
        console.error("Error en la solicitud AJAX:", error);
      },
    });

    return favorites;
  }
  // Función para agregar un favorito
  function addFavorite(jobId) {
    var data = {
      action: "add_favorite",
      jobId: jobId,
    };

    jQuery.ajax({
      url: favs_query_vars.ajax_url,
      method: "POST",
      data: data,
      success: function (response) {
        console.log("add", response);
      },
      error: function (xhr, status, error) {
        // console.error('Error:', error);
      },
    });
  }

  // Función para eliminar un favorito
  function removeFavorite(jobId) {
    var data = {
      action: "remove_favorite",
      user_id: favs_query_vars.currentUserId,
      job_id: jobId,
    };

    jQuery.ajax({
      url: favs_query_vars.ajax_url,
      method: "POST",
      data: data,
      success: function (response) {
        console.log("remove", response);
      },
      error: function (xhr, status, error) {
        console.error("Error:", error);
      },
    });
  }

  // Añadir el evento de clic a cada .fav
  document.querySelectorAll(".fav").forEach(function (favButton) {
    favButton.addEventListener("click", function (event) {
      const listItem = favButton.closest("li");
      if (!listItem) return;

      const itemId = listItem.getAttribute("data-id");
      if (!itemId) return;

      try {
        event.preventDefault();
        event.stopPropagation();
        if (favs_query_vars.isUserLoggedIn) {
          if (listItem.classList.contains("add-fav")) {
            if (!favorites.some((fav) => fav.job_id === itemId)) {
              favorites.push({ job_id: itemId });
              addFavorite(itemId);
            }
          } else if (listItem.classList.contains("remove-fav")) {
            favorites = favorites.filter((fav) => fav.job_id !== itemId);
            removeFavorite(itemId);
          } else {
            console.log("No se encontró la clase add-fav o remove-fav");
          }

          // Cambiar el color del ícono según si está o no en favoritos
          toggleFavIcon(favButton, itemId);

          // Agregar o quitar la clase 'active' en el .item
          toggleItemActiveState(listItem, itemId);
        } else {
          popup_login.style.display = "flex";
        }
      } catch (e) {
        console.log(e);
      }
    });
  });

  //Funciones de la página de saved jobs
  const textElement = document.querySelector("#count-jobs"); // Contenedor de "Visualizando X de Y vacantes"
  // // Actualizar el texto de "Visualizando X de Y vacantes"

  function updateVacantesCount() {
    const visibleCount = ulElement.querySelectorAll("li").length; // Elementos visibles
    const totalCount = favorites.length || 0; // Total de favoritos
    textElement.textContent = `Visualizando ${visibleCount} de ${totalCount} vacantes`;

    if (favorites.length == 0) {
      sinVacantes.style.display = "block";
      contadorVacantes.style.display = "none";
    } else {
      sinVacantes.style.display = "none";
    }
  }

  const ulElement = document.querySelector("#favorites-list"); // Contenedor principal

  if (ulElement) {
    ulElement.addEventListener("click", function (event) {
      if (event.target.closest(".fav")) {
        const jobId = event.target.closest("li").dataset.id; // Obtener el job_id del <li>
        const favButton = event.target.closest(".fav");
        if (favButton) {
          // Obtener el elemento <li> contenedor
          const listItem = favButton.closest("li");
          const itemId = listItem.getAttribute("data-id");
          toggleFavIcon(favButton, itemId);
          toggleItemActiveState(listItem, itemId);
          favorites = favorites.filter((fav) => fav.job_id !== itemId);
          removeFavorite(itemId);
          listItem.remove();
          updateVacantesCount();
        }
      }
    });
  }

  //Función para cambiar el ícono de favorito
  function toggleFavIcon(favButton, id) {
    // Verificar si el id ya está en el array de favoritos

    const isFavorite = favorites.some((fav) => fav.job_id === id);

    if (isFavorite) {
      // Si está en favoritos, agregar clase 'active' al ícono
      favButton.querySelector(".img").classList.add("active");
    } else {
      // Si no está, quitar la clase 'active'
      favButton.querySelector(".img").classList.remove("active");
    }
  }

  //Función para agregar o quitar la clase 'active' al contenedor .item
  function toggleItemActiveState(listItem, id) {
    const isFavorite = favorites.some((fav) => fav.job_id === id);
    if (isFavorite) {
      // Si el id está en los favoritos, agregar la clase 'active' al .item
      listItem.classList.add("active");
      listItem.classList.remove("add-fav"); // Eliminar la clase add-fav
      listItem.classList.add("remove-fav"); // Agregar la clase remove-fav
    } else {
      // Si no está, eliminar la clase 'active'
      listItem.classList.remove("active");
      listItem.classList.add("add-fav"); // Eliminar la clase add-fav
      listItem.classList.remove("remove-fav"); // Agregar la clase remove-fav
    }
  }

  //Revisa el estado de favoritos al cargar la página
  document.querySelectorAll(".fav").forEach(function (favButton) {
    const listItem = favButton.closest("li");
    const itemId = listItem.getAttribute("data-id");

    toggleFavIcon(favButton, itemId);
    toggleItemActiveState(listItem, itemId);
  });

  //Marcar los elementos como 'active' si están en favoritos al cargar la página
  function markItemsAsActive() {
    let favorites = JSON.parse(localStorage.getItem("favorites")) || [];

    document.querySelectorAll(".item").forEach(function (listItem) {
      const itemId = listItem.getAttribute("data-id");
      if (favorites.includes(itemId)) {
        // Si el id está en favoritos, agregar la clase 'active' al .item
        listItem.classList.add("active");
        // También asegurarse de que el ícono de favorito esté activo
        const favButton = listItem.querySelector(".fav");
        toggleFavIcon(favButton, itemId);
      }
    });
  }

  //Ejecutar la función para marcar los ítems como 'active' al cargar la página
  // markItemsAsActive();

  // Carga los primeros 5 saved jobs
  function createSafeSVG(svgString) {
    if (!svgString || typeof svgString !== "string") return null; // Validar entrada

    const parser = new DOMParser();
    const doc = parser.parseFromString(svgString, "image/svg+xml");

    // Validar que el contenido realmente es un SVG
    const svgElement = doc.documentElement;
    if (svgElement.tagName.toLowerCase() !== "svg") return null; // Bloquear contenido sospechoso

    return svgElement; // Devolver solo el SVG sin scripts
  }
  const existPageTemplate = !!document.querySelector(
    ".page-template-saved-jobs"
  );

  if (existPageTemplate) {
    if (favorites.length > 0) {
      let currentIndex = 0; // Índice inicial para paginar

      const ulElement = document.querySelector("#favorites-list"); // Contenedor de los elementos
      const loadMoreButton = document.querySelector("#load-more"); // Botón "Cargar más"

      const loadFavorites = () => {
        if (currentIndex >= favorites.length) return; // No hay más favoritos que cargar

        // Obtener los IDs de los próximos 5 elementos
        const jobIds = favorites
          .slice(currentIndex, currentIndex + 5)
          .map((fav) => fav.job_id);

        currentIndex += 5; // Aumenta el índice para la siguiente carga

        jQuery.ajax({
          url: favs_query_vars.ajax_url,
          method: "POST",
          data: {
            action: "get_favorites",
            favorites: JSON.stringify(jobIds),
          },
          dataType: "json", // Esperamos una respuesta en formato JSON
          success: function (posts) {
            posts.forEach((post) => {
              const li = document.createElement("li");
              li.className = "item remove-fav active";
              li.setAttribute("data-id", post.id);

              // Crear contenedor de imagen
              const imgDiv = document.createElement("div");
              imgDiv.className = "img";
              const img = document.createElement("img");
              img.src = post.image;
              img.alt = "";
              imgDiv.appendChild(img);

              // Crear descripción
              const descDiv = document.createElement("div");
              descDiv.className = "desc";

              const titleLink = document.createElement("a");
              titleLink.href = post.permalink;
              titleLink.textContent = post.title;

              // Crear contenedor de ubicación
              const iconCont1 = document.createElement("div");
              iconCont1.className = "icon-cont";

              const icon1Img = document.createElement("div");
              icon1Img.className = "img";
              icon1Img.appendChild(createSafeSVG(post.location_icon));

              const icon1Text = document.createElement("div");
              icon1Text.className = "text";
              icon1Text.textContent = post.location; // Evita inyecciones

              iconCont1.appendChild(icon1Img);
              iconCont1.appendChild(icon1Text);

              // // Crear contenedor de tiempo
              // const iconCont2 = document.createElement('div');
              // iconCont2.className = 'icon-cont';

              // const icon2Img = document.createElement('div');
              // icon2Img.className = 'img';
              // icon2Img.appendChild(createSafeSVG(post.time_icon));

              // const icon2Text = document.createElement('div');
              // icon2Text.className = 'text';
              // icon2Text.textContent = post.time_text; // Evita inyecciones

              // iconCont2.appendChild(icon2Img);
              // iconCont2.appendChild(icon2Text);

              // Crear botón de favoritos
              const favDiv = document.createElement("div");
              favDiv.className = "fav";

              const favImg = document.createElement("div");
              favImg.className = "img";
              favImg.appendChild(createSafeSVG(post.like_icon));

              favDiv.appendChild(favImg);

              // Agregar elementos al `descDiv`
              descDiv.appendChild(titleLink);
              descDiv.appendChild(iconCont1);
              // descDiv.appendChild(iconCont2);

              // Agregar elementos al `li`
              li.appendChild(imgDiv);
              li.appendChild(descDiv);
              li.appendChild(favDiv);

              ulElement.appendChild(li);
            });

            // Si no hay más elementos, ocultamos el botón
            if (currentIndex >= favorites.length) {
              jQuery("#load-more").hide(); // Usando jQuery para ocultar el botón
            }
            updateVacantesCount();
          },
          error: function (error) {
            console.error("Error al cargar favoritos:", error);
          },
        });
      };

      // Cargar los primeros 5 elementos al cargar la página
      loadFavorites();

      // Verifica si el botón "Cargar más" existe antes de agregarle el evento
      if (loadMoreButton) {
        loadMoreButton.addEventListener("click", loadFavorites);
      }
    }
  }
});
