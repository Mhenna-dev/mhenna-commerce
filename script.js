   const navbar = document.querySelector('.navbar');
    const links = document.querySelectorAll('.navbar a');
    const sections = document.querySelectorAll('section');

    // Fonction pour changer la couleur de la navbar lors du scroll
    window.addEventListener('scroll', () => {
      if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
      } else {
        navbar.classList.remove('scrolled');
      }

      // Surligner les liens actifs
      let currentSection = '';
      sections.forEach(section => {
        const sectionTop = section.offsetTop;
        const sectionHeight = section.offsetHeight;
        if (window.scrollY >= sectionTop - 50 && window.scrollY < sectionTop + sectionHeight) {
          currentSection = section.getAttribute('id');
        }
      });

      links.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href').substring(1) === currentSection) {
          link.classList.add('active');
        }
      });
    });
  