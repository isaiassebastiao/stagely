;(() => {
  document.addEventListener('click', (e) => {
    const toggle = e.target.closest('[data-dropdown-toggle]')
    const optionItem = e.target.closest('.options .item')
    const insideOptions = e.target.closest('.options')
    const aside = document.querySelector('aside')
    const isCollapsed = aside?.classList.contains('collapsed')

    if (toggle) {
      e.stopPropagation()

      // Se o select estiver marcado como desabilitado (input hidden disabled
      // ou toggle com classe 'disabled'), não permite abrir/fechar o dropdown
      const selectWrapper = toggle.closest('.select')
      const hiddenInput = selectWrapper ? selectWrapper.querySelector('input[type="hidden"]') : null
      if (toggle.classList.contains('disabled') || (hiddenInput && hiddenInput.disabled)) return

      const dropdownId = toggle.dataset.dropdownToggle
      const dropdown = document.getElementById(dropdownId)
      if (!dropdown) return

      const isAsideDropdown = toggle.closest('aside')

      // Fecha outros dropdowns no mesmo contexto (aside ou body)
      document.querySelectorAll('.options').forEach((d) => {
        const dIsAside = d.closest('aside')
        if (d !== dropdown && dIsAside === isAsideDropdown) {
          d.classList.remove('active')
        }
      })
      document.querySelectorAll('[data-dropdown-toggle]').forEach((t) => {
        const tIsAside = t.closest('aside')
        if (t !== toggle && tIsAside === isAsideDropdown) {
          t.classList.remove('active')
        }
      })

      // Abre/fecha o atual
      const isActive = toggle.classList.toggle('active')
      dropdown.classList.toggle('active', isActive)

      // Limpar pesquisa se for dropdown de pesquisa
      if (dropdownId === 'search-shortcuts' && !isActive) {
        const searchInput = document.getElementById('headerSearchInput')
        if (searchInput) {
          searchInput.value = ''
          searchInput.dispatchEvent(new Event('input'))
        }
      }
      return
    }

    // Clique num item (marca como ativo)
    if (optionItem) {
      const dropdown = optionItem.closest('.options')

      // Se o select estiver disabled, não permite selecionar opções
      const selectWrapper = dropdown ? dropdown.closest('.select') : null
      const hiddenInput = selectWrapper ? selectWrapper.querySelector('input[type="hidden"]') : null
      if (hiddenInput && hiddenInput.disabled) return

      dropdown.querySelectorAll('.item').forEach((item) => item.classList.remove('active'))
      optionItem.classList.add('active')

      // Limpar pesquisa se for dropdown de pesquisa
      if (dropdown.id === 'search-shortcuts') {
        const searchInput = document.getElementById('headerSearchInput')
        if (searchInput) {
          searchInput.value = ''
          searchInput.dispatchEvent(new Event('input'))
        }
        dropdown.classList.remove('active')
        const toggle = document.querySelector(`[data-dropdown-toggle="search-shortcuts"]`)
        toggle?.classList.remove('active')
        return
      }

      // Se aside estiver colapsado, fecha o dropdown após clicar
      if (isCollapsed) {
        dropdown.classList.remove('active')
        const toggle = document.querySelector(`[data-dropdown-toggle="${dropdown.id}"]`)
        toggle?.classList.remove('active')
      }

      return
    }

    if (insideOptions && !isCollapsed) {
      return
    }

    // Clique fora de tudo
    document.querySelectorAll('.options').forEach((d) => {
      // Se aside colapsado: fecha todos
      if (isCollapsed || !d.closest('aside')) d.classList.remove('active')
    })
    document.querySelectorAll('[data-dropdown-toggle]').forEach((t) => {
      if (isCollapsed || !t.closest('aside')) t.classList.remove('active')
    })
  })
})()
