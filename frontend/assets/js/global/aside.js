document.addEventListener('DOMContentLoaded', () => {
  const btnCollapse = document.getElementById('btn-collapse')
  const aside = document.querySelector('aside')

  const isCollapsed = localStorage.getItem('asideCollapsed')
  if (aside && isCollapsed === 'true') {
    aside.classList.add('collapsed')
  }

  if (btnCollapse && aside) {
    btnCollapse.addEventListener('click', () => {
      aside.classList.toggle('collapsed')

      const collapsed = aside.classList.contains('collapsed')
      localStorage.setItem('asideCollapsed', collapsed)
    })
  }

  ;(async () => {
    try {
      const response = await fetch('/stagely/backend/routes/getRole.php')
      const result = await response.json()
      const nav = document.getElementById('aside-menu')
      if (!nav) return

      nav.innerHTML = ''

      if (result.role === 'Escola') {
        nav.innerHTML = `
       <a href="/stagely/frontend/pages/inicio/index.php" class="item1" data-title="Dashboard">
      <svg>
        <use href="#home" />
      </svg>
      <p>Dashboard</p>
      <span></span>
    </a>

    <a href="/stagely/frontend/pages/listar-empresas/index.php" class="item1" data-title="Empresas">
      <svg>
        <use href="#building" />
      </svg>
      <p>Empresas</p>
      <span></span>
    </a>
    <div class="dropdown">
      <button class="open" data-dropdown-toggle="internships" data-title="Estágios">
        <div>
          <svg>
            <use href="#calendar" />
          </svg>
          <p>Estágios</p>
        </div>

        <svg class="chevron-icon">
          <use href="#chevron-down" />
        </svg>
      </button>

      <div class="options" id="internships">
        <a href="/stagely/frontend/pages/listar-estagios/index.php?curso=ti&dropdown=internships" class="item" data-course="ti">
          <p>Técnico de Informática</p>
          <span></span>
        </a>
        <a href="/stagely/frontend/pages/listar-estagios/index.php?curso=bq&dropdown=internships" class="item" data-course="bq">
          <p>Bioquímica</p>
          <span></span>
        </a>
        <a href="/stagely/frontend/pages/listar-estagios/index.php?curso=mm&dropdown=internships" class="item" data-course="mm">
          <p>Máquinas e Motores</p>
          <span></span>
        </a>
        <a href="/stagely/frontend/pages/listar-estagios/index.php?curso=dp&dropdown=internships" class="item" data-course="dp">
          <p>Desenhador Projectista</p>
          <span></span>
        </a>
        <a href="/stagely/frontend/pages/listar-estagios/index.php?curso=ie&dropdown=internships" class="item" data-course="ei">
          <p>Energia e Instalações Eléctricas</p>
          <span></span>
        </a>
      </div>
    </div>
    <div class="dropdown">
      <button class="open" data-dropdown-toggle="avaliations" data-title="Avaliações">
        <div>
          <svg>
            <use href="#form" />
          </svg>
          <p>Avaliações</p>
        </div>

        <svg class="chevron-icon">
          <use href="#chevron-down" />
        </svg>

        <span></span>
      </button>

      <div class="options" id="avaliations">
       <a href="/stagely/frontend/pages/listar-avaliacoes/index.php?curso=ti&dropdown=avaliations" class="item" data-course="ti">
          <p>Técnico de Informática</p>
          <span></span>
        </a>
        <a href="/stagely/frontend/pages/listar-avaliacoes/index.php?curso=bq&dropdown=avaliations" class="item" data-course="bq">
          <p>Bioquímica</p>
          <span></span>
        </a>
        <a href="/stagely/frontend/pages/listar-avaliacoes/index.php?curso=mm&dropdown=avaliations" class="item" data-course="mm">
          <p>Máquinas e Motores</p>
          <span></span>
        </a>
        <a href="/stagely/frontend/pages/listar-avaliacoes/index.php?curso=dp&dropdown=avaliations" class="item" data-course="dp">
          <p>Desenhador Projectista</p>
          <span></span>
        </a>
        <a href="/stagely/frontend/pages/listar-avaliacoes/index.php?curso=ie&dropdown=avaliations" class="item" data-course="ei">
          <p>Energia e Instalações Eléctricas</p>
          <span></span>
        </a>
      </div>
    </div>
      `
      } else if (result.role === 'Empresa') {
        nav.innerHTML = `
        <a href="/stagely/frontend/pages/inicio/index.php" data-title="Dashboard">
      <svg>
        <use href="#home" />
      </svg>
      <p>Dashboard</p>
      <span></span>
    </a>
       <a href="/stagely/frontend/pages/listar-estagiarios/index.php" data-title="Estagiários">
      <svg>
        <use href="#student" />
      </svg>
      <p>Estagiários</p>
      <span></span>
    </a>
       <a href="/stagely/frontend/pages/listar-avaliacoes/index.php" data-title="Avaliações">
      <svg>
        <use href="#form" />
      </svg>
      <p>Avaliações</p>
      <span></span>
    </a>
      `
      }

      // ─── Marcar links ativos ───────────────────────────────────────────────

      const sectionAliases = {
        avaliacao: 'estagiarios'
      }

      function getSectionFromPath(pathname) {
        const segment = pathname.split('/').filter(Boolean).slice(-2)[0] || ''
        const raw = segment.replace(/^(listar|cadastrar|editar|visualizar|ver|enviar)-?/, '').toLowerCase()
        return sectionAliases[raw] || raw
      }

      const currentPath = window.location.pathname
      const currentSection = getSectionFromPath(currentPath)
      const currentParams = new URLSearchParams(window.location.search)

      nav.querySelectorAll('a').forEach((link) => {
        const linkUrl = new URL(link.href)
        const linkPath = linkUrl.pathname
        const linkSection = getSectionFromPath(linkPath)
        const linkParams = linkUrl.searchParams

        const relevantLinkParams = [...linkParams.entries()].filter(([key]) => key !== 'dropdown')

        if (relevantLinkParams.length > 0) {
          // FIX 1: usa sectionMatch em vez de pathMatch
          const sectionMatch = currentSection.includes(linkSection) || linkSection.includes(currentSection)
          const paramsMatch = relevantLinkParams.every(([key, value]) => currentParams.get(key) === value)

          if (sectionMatch && paramsMatch) {
            link.classList.add('active')

            // FIX 2: abre o dropdown pai automaticamente se o link estiver ativo
            const parentOptions = link.closest('.options')
            if (parentOptions) {
              const dropdownId = parentOptions.id
              const toggle = document.querySelector(`[data-dropdown-toggle="${dropdownId}"]`)
              if (toggle) {
                toggle.classList.add('active')
                parentOptions.classList.add('active')
              }
            }
          }
        } else {
          if (link.href === window.location.href || (linkSection && (currentSection.includes(linkSection) || linkSection.includes(currentSection)))) {
            link.classList.add('active')
          }
        }
      })

      // ─── Verificar se há parâmetro dropdown na URL (mantido para compatibilidade) ───

      const urlParams = new URLSearchParams(window.location.search)
      const dropdownParam = urlParams.get('dropdown')
      if (dropdownParam) {
        const dropdown = document.getElementById(dropdownParam)
        if (dropdown) {
          const toggle = document.querySelector(`[data-dropdown-toggle="${dropdownParam}"]`)
          if (toggle) {
            toggle.classList.add('active')
            dropdown.classList.add('active')
          }
        }
      }
    } catch (error) {
      console.error('Erro ao carregar aside:', error)
    }
  })()
})
