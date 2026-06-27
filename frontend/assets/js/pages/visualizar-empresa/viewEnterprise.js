;(function () {
  document.addEventListener('DOMContentLoaded', () => {
    function getEnterpriseIdFromURL() {
      const params = new URLSearchParams(window.location.search)
      return params.get('id')
    }

    async function loadEnterprise(enterpriseId) {
      try {
        const response = await fetch(`/stagely/backend/routes/adminRoutes.php?action=getEnterprise&id=${enterpriseId}`)

        if (!response.ok) {
          throw new Error('Erro na requisição')
        }

        const result = await response.json()

        if (!result.success) {
          showAlert(result.message, 'error')
          return
        }

        const enterprise = result.data

        document.querySelector('.header h3').textContent = enterprise.name || '-'

        const initial = enterprise.name ? enterprise.name.charAt(0).toUpperCase() : '-'

        const photoEl = document.querySelector('.photo')
        const photoH4 = photoEl.querySelector('h4')

        photoH4.textContent = enterprise.photo ? '' : initial

        if (enterprise.photo) {
          photoEl.style.backgroundImage = `url('${enterprise.photo}')`
        }

        const statusEl = document.querySelector('.status span')
        const statusBox = document.querySelector('.status')

        const isActive = enterprise.status === 'Activo'

        statusEl.textContent = isActive ? 'Activo' : 'Inactivo'
        statusBox.classList.toggle('active', isActive)
        statusBox.classList.toggle('inactive', !isActive)

        const items = document.querySelectorAll('.info .item p')
        const data = [enterprise.email || '-', '********', enterprise.area_activity?.map((a) => a.area).join(', ') || '-', enterprise.vacancies || '-', enterprise.hood || '-', enterprise.street || '-']

        items.forEach((el, index) => {
          el.textContent = data[index] || '-'
        })
      } catch (error) {
        console.error(error)
        showAlert('Erro ao carregar empresa.', 'error')
      }
    }

    const enterpriseId = getEnterpriseIdFromURL()

    if (enterpriseId) {
      loadEnterprise(enterpriseId)
    } else {
      showAlert('ID da empresa não encontrado.', 'error')
    }
  })
})()
