;(() => {
  const tbody = document.getElementById('tableBody')
  const searchInput = document.getElementById('searchInput')
  const pagination = document.querySelector('.pagination')
  const params = new URLSearchParams(window.location.search)
  const curso = params.get('curso') || 'ti'

  let internships = []
  let filteredInternships = []
  let currentPage = 1
  const rowsPerPage = 6

  const normalizeText = (text) =>
    text
      .toLowerCase()
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .trim()

  async function loadInternships() {
    const response = await fetch(`/stagely/backend/routes/adminRoutes.php?curso=${curso}&action=listInternship`)
    if (!response.ok) throw new Error('Erro ao carregar estágios')

    internships = await response.json()
    internships = internships.enterprises
    console.log(internships)
    filteredInternships = [...internships]

    renderTablePage(1)
    renderPagination()
  }

  function renderTablePage(page) {
    currentPage = page
    const start = (page - 1) * rowsPerPage
    const end = start + rowsPerPage
    const pageData = filteredInternships.slice(start, end)
    // Contagem (singular/plural)
    const count = filteredInternships.length
    const text = count === 1 ? '1 estágio cadastrado' : `${count} estágios cadastrados`
    document.querySelector('.search-wrapper h3').textContent = text

    tbody.innerHTML = ''

    if (count === 0) {
      tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;color:var(--grey-light);font-family:var(--light);">Nenhum estágio encontrado</td></tr>`
      return
    }

    pageData.forEach((internship) => {
      const tr = document.createElement('tr')
      tr.innerHTML = `
        ${
          internship.imagem_perfil
            ? `<td data-Label="Nome">
              <div style="display:flex;align-items:center;">
                <img class="photo" src="${internship.imagem_perfil}">${internship.nome}
              </div>
            </td>`
            : `<td><span class="photo">${internship.nome[0]}</span>${internship.nome}</td>`
        }
        <td data-Label="Área de Estágio">${internship.area_estagio}</td>

        <td data-Label="Período">${internship.datetime.inicio ? internship.datetime.inicio.split('-').reverse().join('/') : ''} - ${internship.datetime.fim ? internship.datetime.fim.split('-').reverse().join('/') : ''}</td>

        <td data-Label="Horário">${internship.datetime.entrada.slice(0, 5)}-${internship.datetime.saida.slice(0, 5)}</td>
        <td data-Label="Status"><span class="status ${internship.status === 'Em execução' ? 'progress' : internship.status === 'Concluído' ? 'finished' : 'pending'}">${internship.status}
          </span></td>
          <td id="actions">
            <div class="actions">
              <button class="open" data-dropdown-toggle="data-${internship.id + internship.nome + internship.area_estagio + curso}">
                <svg><use href="#dots" /></svg>
              </button>

              <div class="options" id="data-${internship.id + internship.nome + internship.area_estagio + curso}">
                <a href="/stagely/frontend/pages/visualizar-estagio/index.php?id=${internship.id}&area=${internship.area_estagio}&curso=${curso}" class="item">
                  <svg><use href="#eye" /></svg>
                  <span>Visualizar</span>
                </a>
                <a href="/stagely/frontend/pages/editar-estagio/index.php?id=${internship.id}&area=${internship.area_estagio}&curso=${curso}" class="item">
                  <svg><use href="#pencil" /></svg>
                  <span>Editar</span>
                </a>
                <button class="item delete" data-id="${internship.id}" data-area="${internship.area_estagio}">
                  <svg><use href="#trash" /></svg>
                  <span>Excluir</span>
                </button>
              </div>
            </div>
          </td>
      `

      const deleteBtn = tr.querySelector('.delete')
      deleteBtn.addEventListener('click', () => {
        const id = parseInt(deleteBtn.dataset.id)
        const area = deleteBtn.dataset.area
        //depois enviar essa área para o back
        confirmDeleteInternship(id, area)
      })

      tbody.appendChild(tr)
    })

    // Atualiza a paginação
    renderPagination()
  }

  function renderPagination() {
    pagination.innerHTML = ''
    const pageCount = Math.ceil(filteredInternships.length / rowsPerPage)
    if (pageCount <= 1) return // não mostra se só tiver 1 página

    const prevBtn = document.createElement('button')
    prevBtn.disabled = currentPage === 1
    prevBtn.innerHTML = `<svg><use href="#arrow-left" /></svg>`
    prevBtn.addEventListener('click', () => renderTablePage(currentPage - 1))
    pagination.appendChild(prevBtn)

    for (let i = 1; i <= pageCount; i++) {
      const btn = document.createElement('button')
      btn.textContent = i
      if (i === currentPage) btn.classList.add('active')
      btn.addEventListener('click', () => renderTablePage(i))
      pagination.appendChild(btn)
    }

    const nextBtn = document.createElement('button')
    nextBtn.disabled = currentPage === pageCount
    nextBtn.innerHTML = `<svg><use href="#arrow-right" /></svg>`
    nextBtn.addEventListener('click', () => renderTablePage(currentPage + 1))
    pagination.appendChild(nextBtn)
  }

  // Atualiza tabela após filtro ou ordenação
  function updateFiltered(data) {
    filteredInternships = data
    renderTablePage(1)
  }

  // Filtra tabela ao digitar
  searchInput.addEventListener('input', () => {
    const term = normalizeText(searchInput.value)

    const filtered = internships.filter((internship) => {
      const nome = normalizeText(internship.nome || '')

      const areas = normalizeText(internship.area_estagio)

      const periodo = internship.datetime ? normalizeText(`${internship.datetime.inicio} ${internship.datetime.fim}`) : ''

      const status = normalizeText(internship.status || '')

      return nome.includes(term) || areas.includes(term) || periodo.includes(term) || status.includes(term)
    })

    updateFiltered(filtered)
  })

  // Ordenação /Filtragem
  document.querySelectorAll('#data-filter .item').forEach((item) => {
    item.addEventListener('click', () => {
      const value = item.dataset.value
      let result = [...internships]

      switch (value) {
        case 'az':
          result.sort((a, b) => a.nome.localeCompare(b.nome))
          break
        case 'za':
          result.sort((a, b) => b.nome.localeCompare(a.nome))
          break
        case 'em-execucao':
          result = result.filter((internship) => internship.status === 'Em execução')
          break
        case 'concluido':
          result = result.filter((internship) => internship.status === 'Concluído')
          break
        case 'pendente':
          result = result.filter((internship) => internship.status === 'Pendente')
          break
        default:
          result = [...internships]
      }

      updateFiltered(result)
    })
  })

  window.onInternshipDeleted = function (id, area) {
    internships = internships.filter((e) => e.id+e.area_estagio !== id+area)
    filteredInternships = filteredInternships.filter((e) => e.id+e.area_estagio !== id+area)
    console.log(internships, filteredInternships)

    const totalPages = Math.ceil(filteredInternships.length / rowsPerPage)
    if (currentPage > totalPages) currentPage = totalPages || 1

    renderTablePage(currentPage)
  }

  document.addEventListener('click', (e) => {
    const actionBtn = e.target.closest('.actions .open')

    if (!actionBtn) {
      document.querySelectorAll('tbody tr.selected').forEach((tr) => tr.classList.remove('selected'))
      return
    }

    const tr = actionBtn.closest('tr')

    document.querySelectorAll('tbody tr.selected').forEach((row) => {
      if (row !== tr) row.classList.remove('selected')
    })

    tr.classList.toggle('selected')
  })

  loadInternships()
})()
