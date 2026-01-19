document.addEventListener("DOMContentLoaded", function () {
  const buttons = document.querySelectorAll(".lang-btn");
  const savedLang = localStorage.getItem("lang") || "en";

  setActiveLang(savedLang);

  buttons.forEach(btn => {
    btn.addEventListener("click", () => {
      const lang = btn.dataset.lang;
      localStorage.setItem("lang", lang);
      setActiveLang(lang);
      changeLanguage(lang);
    });
  });

  function setActiveLang(lang) {
    buttons.forEach(b => b.classList.remove("active"));
    document.querySelector(`.lang-btn[data-lang="${lang}"]`).classList.add("active");
  }


});