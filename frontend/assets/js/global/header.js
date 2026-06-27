;(() => {
  document.addEventListener('DOMContentLoaded', async () => {
    try {
      const response = await fetch('/stagely/backend/routes/getRole.php')
      const result = await response.json()

      const shortcuts = document.getElementById('shortcuts')
      const nav = document.querySelector('#menu nav')

      if (result.role === 'Empresa' && shortcuts) {
        shortcuts.innerHTML = `
          <a href="/stagely/frontend/pages/enviar-ficha/index.php" class="item" data-search="enviar ficha">
            <svg><use href="#cloud"></use></svg>
            <span>Enviar ficha</span>
          </a>
        `
      }

      if (nav) {
        nav.innerHTML =
          result.role === 'Empresa'
            ? `
          <a href="/stagely/frontend/pages/inicio/index.php" class="item1">
            <svg class="nav-icon"><use href="#home" /></svg>
            <p>Dashboard</p>
          </a>
          <a href="/stagely/frontend/pages/listar-estagiarios/index.php" class="item1">
            <svg class="nav-icon"><use href="#student" /></svg>
            <p>Estagiários</p>
          </a>
          <a href="/stagely/frontend/pages/listar-avaliacoes/index.php" class="item1">
            <svg class="nav-icon"><use href="#form" /></svg>
            <p>Avaliações</p>
          </a>
        `
            : `
          <a href="/stagely/frontend/pages/inicio/index.php" class="item1">
            <svg class="nav-icon"><use href="#home" /></svg>
            <p>Dashboard</p>
          </a>
          <a href="/stagely/frontend/pages/listar-empresas/index.php" class="item2">
            <svg class="nav-icon"><use href="#building" /></svg>
            <p>Empresas</p>
          </a>
          <div class="dropdown">
            <button class="open" data-dropdown-toggle="header-internships">
              <div class="left">
                <svg class="nav-icon"><use href="#calendar" /></svg>
                <p>Estágios</p>
              </div>
              <svg class="chevron-icon"><use href="#chevron-down" /></svg>
            </button>
            <div class="options" id="header-internships">
              <a href="/stagely/frontend/pages/listar-estagios/index.php?curso=ti&dropdown=internships" class="item" data-course="ti"><p>Técnico de Informática</p></a>
              <a href="/stagely/frontend/pages/listar-estagios/index.php?curso=bq&dropdown=internships" class="item" data-course="bq"><p>Bioquímica</p></a>
              <a href="/stagely/frontend/pages/listar-estagios/index.php?curso=mm&dropdown=internships" class="item" data-course="mm"><p>Máquinas e Motores</p></a>
              <a href="/stagely/frontend/pages/listar-estagios/index.php?curso=dp&dropdown=internships" class="item" data-course="dp"><p>Desenhador Projectista</p></a>
              <a href="/stagely/frontend/pages/listar-estagios/index.php?curso=ie&dropdown=internships" class="item" data-course="ei"><p>Energia e Instalações Eléctricas</p></a>
            </div>
          </div>
          <div class="dropdown">
            <button class="open" data-dropdown-toggle="header-avaliations">
              <div class="left">
                <svg class="nav-icon"><use href="#form" /></svg>
                <p>Avaliações</p>
              </div>
              <svg class="chevron-icon"><use href="#chevron-down" /></svg>
            </button>
            <div class="options" id="header-avaliations">
              <a href="/stagely/frontend/pages/listar-avaliacoes/index.php?curso=ti&dropdown=avaliations" class="item" data-course="ti"><p>Técnico de Informática</p></a>
              <a href="/stagely/frontend/pages/listar-avaliacoes/index.php?curso=bq&dropdown=avaliations" class="item" data-course="bq"><p>Bioquímica</p></a>
              <a href="/stagely/frontend/pages/listar-avaliacoes/index.php?curso=mm&dropdown=avaliations" class="item" data-course="mm"><p>Máquinas e Motores</p></a>
              <a href="/stagely/frontend/pages/listar-avaliacoes/index.php?curso=dp&dropdown=avaliations" class="item" data-course="dp"><p>Desenhador Projectista</p></a>
              <a href="/stagely/frontend/pages/listar-avaliacoes/index.php?curso=ie&dropdown=avaliations" class="item" data-course="ei"><p>Energia e Instalações Eléctricas</p></a>
            </div>
          </div>
        `
      }

      const ans = await fetch('/stagely/backend/routes/userRoutes.php?action=getProfile')
      const pic = await ans.json()

      const profile = document.querySelector('.photoProfile')
      if (profile) {
        profile.style.backgroundImage = pic.profile_pic ? `url(${pic.profile_pic})` : "url('/stagely/frontend/assets/images/placeholder.webp')"
      }
    } catch (error) {
      console.error(error)
    }

    const searchInput = document.getElementById('headerSearchInput')
    const searchDropdown = document.getElementById('search-shortcuts')
    const noResults = searchDropdown?.querySelector('.no-results')

    if (searchInput && searchDropdown) {
      searchInput.addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase().trim()
        const items = searchDropdown.querySelectorAll('.item')
        const headings = searchDropdown.querySelectorAll('h3')

        let hasAnyVisibleItem = false

        items.forEach((item) => {
          const searchText = item.dataset.search.toLowerCase()
          const itemText = item.querySelector('span').textContent.toLowerCase()

          if (query === '' || searchText.includes(query) || itemText.includes(query)) {
            item.style.display = ''
            hasAnyVisibleItem = true
          } else {
            item.style.display = 'none'
          }
        })

        // Mostrar/esconder títulos
        headings.forEach((heading) => {
          let nextItem = heading.nextElementSibling
          let hasVisibleItems = false

          while (nextItem && nextItem.tagName !== 'H3') {
            if (nextItem.classList.contains('item') && nextItem.style.display !== 'none') {
              hasVisibleItems = true
              break
            }
            nextItem = nextItem.nextElementSibling
          }

          heading.style.display = hasVisibleItems ? '' : 'none'
        })

        // Sem resultados
        if (query !== '' && !hasAnyVisibleItem) {
          if (noResults) noResults.style.display = ''
        } else {
          if (noResults) noResults.style.display = 'none'
        }
      })
    }
  })
})()

const menu_icon = document.querySelector('.menu-icon')
const close_menu_icon = document.querySelector('.close-menu-icon')
const menu = document.querySelector('menu')

const slideIn = () => {
  menu.classList.remove('menu-off')
  menu.classList.add('menu-on')
}

const slideOut = () => {
  menu.classList.remove('menu-on')
  menu.classList.add('menu-off')
}

if (menu_icon) {
  menu_icon.addEventListener('click', slideIn)
}

if (close_menu_icon) {
  close_menu_icon.addEventListener('click', slideOut)
}
