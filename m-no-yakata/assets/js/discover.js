document.addEventListener('DOMContentLoaded', () => {
  const reviewButtons = document.querySelectorAll('.mno-btn-review');

  if (!reviewButtons.length) {
    return;
  }

  const deactivateSheets = (currentSheet) => {
    document.querySelectorAll('.mno-bottom-sheet.active').forEach((sheet) => {
      if (sheet !== currentSheet) {
        sheet.classList.remove('active');
      }
    });
  };

  reviewButtons.forEach((button) => {
    const sheetId = button.getAttribute('data-sheet-target');

    if (!sheetId) {
      return;
    }

    const sheet = document.getElementById(sheetId);

    if (!sheet) {
      return;
    }

    const overlay = sheet.querySelector('.mno-sheet-overlay');
    const closeBtn = sheet.querySelector('.mno-sheet-close');

    const openSheet = () => {
      deactivateSheets(sheet);
      sheet.classList.add('active');
    };

    const closeSheet = () => {
      sheet.classList.remove('active');
    };

    button.addEventListener('click', openSheet);

    if (overlay) {
      overlay.addEventListener('click', closeSheet);
    }

    if (closeBtn) {
      closeBtn.addEventListener('click', closeSheet);
    }
  });
});