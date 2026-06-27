;(() => {
  async function deleteInternship(id, area) {
    const btn = document.querySelector(`.delete[data-id="${id}"][data-area="${area}"]`)
    const span = btn?.querySelector('span')

    const urlParam = new URLSearchParams(window.location.search)
    const course = urlParam.get('curso') || ''

    if (btn) {
      btn.disabled = true
      if (span) span.textContent = 'Eliminando...'
      btn.style.color = 'var(--red)'
    }
    console.log(id, area)
    try {
      const response = await fetch(`/stagely/backend/routes/adminRoutes.php?action=deleteInternship`, {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id, area, curso: course })
      })

      if (!response.ok) {
        throw new Error('Erro na requisição')
      }

      const result = await response.json()
      console.log(result)

      if (result.success) {
        showAlert(result.message, 'success')

        if (typeof window.onInternshipDeleted === 'function') {
          window.onInternshipDeleted(id, area)
        }
      } else {
        showAlert(result.message, 'error')
      }
    } catch (error) {
      console.error(error)
      showAlert('Erro de conexão. Tente novamente.', 'error')
    } finally {
      if (btn) {
        btn.disabled = false
        if (span) span.textContent = 'Excluir'
      }
    }
  }

  function confirmDeleteInternship(id, area) {
    openConfirmModal({
      type: 'delete',
      title: 'Eliminar estágio',
      message: 'Tem certeza que deseja eliminar este estágio?',
      onConfirm: () => deleteInternship(id, area)
    })
  }

  window.confirmDeleteInternship = confirmDeleteInternship
})()
