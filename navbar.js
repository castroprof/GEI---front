let lastScrollTop = 0;
  const topbar = document.getElementById("topbar");
  window.addEventListener("scroll", function () {
    const currentScroll = window.pageYOffset || document.documentElement.scrollTop;
    if (currentScroll > lastScrollTop) {
      topbar.style.top = "-80px"; // esconde
    } else {
      topbar.style.top = "0"; // mostra
    }
    lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
  });