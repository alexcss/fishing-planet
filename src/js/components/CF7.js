const CF7 = () => {
  const forms = document.querySelectorAll('.wpcf7')

  if (!forms.length) {
    return
  }

  forms.forEach((form) => {
    const inputFileWrap = form.querySelector('[data-input-file]')

    if (!inputFileWrap) return

    let inputFile = inputFileWrap.querySelector("[type='file']")

    if (!inputFile) return

    inputFile.addEventListener('change', (e) => {
      let filename = e.target.files[0]?.name

      if (filename) {
        inputFileWrap.dataset.inputFile = filename
        inputFileWrap.classList.add('has-file')
      } else {
        inputFileWrap.classList.remove('has-file')
      }
    })

    form.addEventListener('wpcf7mailsent', () => {
      if (inputFile) {
        inputFileWrap.classList.remove('has-file')
        inputFileWrap.dataset.inputFile = ''
      }
    })
  })

  forms.forEach((form) => {
    const formMessage = form.querySelector('[data-form-message]')

    if (!formMessage) return

    formMessage.addEventListener('click', () => {
      const formEl = form.querySelector('.wpcf7-form')
      if (!formEl) return
      formEl.classList.remove('sent')
    })
  })
}

export default CF7
