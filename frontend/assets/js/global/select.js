;(() => {
  const normalizeText = (text) =>
    text
      .toLowerCase()
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .trim()

  const getCleanText = (element) => {
    const clone = element.cloneNode(true)
    clone.querySelectorAll('.status-circle, .area-icon').forEach((el) => el.remove())
    return clone.textContent.trim()
  }

  const selectsState = []

  document.querySelectorAll('.select').forEach((select) => {
    const isMultiple = select.hasAttribute('data-multiple')

    const selected = select.querySelector('.selected')
    const selectedText = selected.querySelector('.text')
    const optionsWrapper = select.querySelector('.options')
    const list = optionsWrapper.querySelector('.list')
    const hiddenInput = select.querySelector('input[type="hidden"]')
    const searchInput = optionsWrapper.querySelector('.search input')

    let selectedValues = []

    const createCheckIcon = () => {
      const checkSvg = document.createElementNS('http://www.w3.org/2000/svg', 'svg')
      checkSvg.setAttribute('viewBox', '0 0 24 24')
      checkSvg.style.width = '16px'
      checkSvg.style.height = '16px'
      checkSvg.style.marginLeft = '8px'
      const poly = document.createElementNS('http://www.w3.org/2000/svg', 'polyline')
      poly.setAttribute('points', '20 6 9 17 4 12')
      poly.setAttribute('stroke', 'var(--green)')
      poly.setAttribute('stroke-width', '2')
      poly.setAttribute('fill', 'none')
      checkSvg.appendChild(poly)
      return checkSvg
    }

    const initializeSelection = () => {
      const value = hiddenInput.value
      if (!value) return

      if (isMultiple) {
        const values = value
          .split(',')
          .map((item) => item.trim())
          .filter(Boolean)

        selectedValues = values
        list.querySelectorAll('.item').forEach((opt) => {
          const itemValue = opt.dataset.value || opt.textContent.trim()
          const shouldBeSelected = values.includes(itemValue)
          const svg = opt.querySelector('svg')

          if (shouldBeSelected) {
            opt.style.color = 'var(--white)'
            if (!svg) opt.appendChild(createCheckIcon())
          } else {
            opt.style.color = ''
            if (svg) svg.remove()
          }
        })

        if (selectedValues.length === 0) {
          selectedText.textContent = 'Selecionar'
          selected.classList.remove('filled')
        } else if (selectedValues.length === 1) {
          const item = list.querySelector(`.item[data-value="${selectedValues[0]}"]`)
          selectedText.textContent = item ? getCleanText(item) : selectedValues[0]
          selected.classList.add('filled')
        } else {
          selectedText.textContent = `${selectedValues.length} selecionados`
          selected.classList.add('filled')
        }
      } else {
        const option = list.querySelector(`.item[data-value="${value}"]`)
        if (!option) return

        list.querySelectorAll('.item').forEach((opt) => {
          opt.style.color = ''
          const svg = opt.querySelector('svg')
          if (svg) svg.remove()
        })

        option.style.color = 'var(--white)'
        selectedValues = [value]
        selectedText.textContent = getCleanText(option)
        selected.classList.add('filled')
      }
    }

    list.addEventListener('click', (e) => {
      const option = e.target.closest('.item')
      if (!option) return

      const value = option.dataset.value || option.textContent.trim()

      if (isMultiple) {
        const index = selectedValues.indexOf(value)
        if (index > -1) {
          // Deseleciona
          selectedValues.splice(index, 1)
          option.style.color = ''
          const svg = option.querySelector('svg')
          if (svg) svg.remove()
        } else {
          // Seleciona
          selectedValues.push(value)
          option.style.color = 'var(--white)'

          // Adiciona svg de check
          const checkSvg = createCheckIcon()
          option.appendChild(checkSvg)
        }

        hiddenInput.value = selectedValues.join(',')
        selectedText.textContent = selectedValues.length === 0 ? 'Selecionar' : selectedValues.length === 1 ? getCleanText(list.querySelector(`.item[data-value="${selectedValues[0]}"]`)) : `${selectedValues.length} selecionados`

        selected.classList.toggle('filled', selectedValues.length > 0)
      } else {
        // Single select
        list.querySelectorAll('.item').forEach((opt) => {
          opt.style.color = ''
          const svg = opt.querySelector('svg')
          if (svg) svg.remove()
        })
        option.style.color = 'var(--white)'

        selectedValues = [value]
        hiddenInput.value = value
        selectedText.textContent = getCleanText(option)

        selected.classList.add('filled')
        selected.classList.remove('active')
        optionsWrapper.classList.remove('active')
      }

      if (searchInput) searchInput.value = ''

      const noRes = list.querySelector('.no-results')
      if (noRes) noRes.remove()
    })

    // Pesquisa
    if (searchInput) {
      searchInput.addEventListener('input', () => {
        const filter = normalizeText(searchInput.value)
        let visibleCount = 0

        list.querySelectorAll('.item').forEach((option) => {
          const match = normalizeText(option.textContent).includes(filter)
          option.style.display = match ? '' : 'none'
          if (match) visibleCount++
        })

        const old = list.querySelector('.no-results')
        if (old) old.remove()

        if (visibleCount === 0) {
          const li = document.createElement('li')
          li.className = 'no-results'
          li.textContent = 'Sem resultados'
          list.appendChild(li)
        }
      })
    }

    initializeSelection()
    selectsState.push(initializeSelection)
  })

  window.initializeCustomSelects = () => selectsState.forEach((init) => init())
})()
