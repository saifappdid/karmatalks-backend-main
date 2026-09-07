

/******/ (() => {
      // webpackBootstrap
      var __webpack_exports__ = {};
      /*!**************************************!*\
          !*** ./resources/js/core/scripts.js ***!
          \**************************************/
      window.addEventListener("load", function () {

        var e = document.getElementById("custom-loader");
        document.body.removeChild(e),
          $(".dataTables_scrollBody").length > 0 &&
            new PerfectScrollbar(".dataTables_scrollBody"),
          document.addEventListener("keydown", function (e) {
            "/" === e.key &&
              e.ctrlKey &&
              (e.preventDefault(),
              document.querySelector(".nav-link-search").click());
          });
      });
      /******/
    })();
    