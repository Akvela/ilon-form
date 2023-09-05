const buttonBurger = document.querySelector('.ham');
const popupNav = document.querySelector('.nav-mobile');
const buttonOpenForm = document.querySelector('.main-button');
const popupFeedback = document.querySelector('.popup');
const popupContainerOverlay = document.querySelector('popup-content::after');
const buttonExit = document.querySelector('.popup-close');
const form = document.querySelector('.form');
const popupOverlay = document.querySelector('.popup-overlay');
const errorMessage = document.querySelector('.form-error');
const regexPhone = /^(\+7|7|8)?[\s\-]?\(?[489][0-9]{2}\)?[\s\-]?[0-9]{3}[\s\-]?[0-9]{2}[\s\-]?[0-9]{2}$/;
const regexEmail = /^[A-Z0-9._%+-]+@[A-Z0-9-]+.+.[A-Z]{2,4}$/i;

function pressBurger(event) { 
  event.target.classList.toggle('active'); 
  popupNav.classList.toggle('nav-mobile_open');
}; 

function pressStartTravel() { 
  popupFeedback.classList.add('popup_is-opened');
}; 

function pressClosePopupFeedback() { 
  popupFeedback.classList.remove('popup_is-opened');
  form.reset();
}; 

function isEmail(str) {
  return /^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$/.test(str);
}

function isPhone(str) {
  return /^(\+7|7|8)?[\s\-]?\(?[489][0-9]{2}\)?[\s\-]?[0-9]{3}[\s\-]?[0-9]{2}[\s\-]?[0-9]{2}$/.test(str);
}

function showInputError(input) {
  input.classList.add('form-input-error');
};

function hideInputError(input) {
  input.classList.remove('form-input-error');
};

function validationForm(form) {
  let error = 0;
  const inputs = form.querySelectorAll('.form-input');

  for (let i = 0; i < inputs.length; i++) {
    const input = inputs[i];
    hideInputError(input);
    if (input.value === '') {
      showInputError(input);
      error++;
    } else {
      if (input.classList.contains('email-phone')) {
        if (!isEmail(input.value) && !isPhone(input.value)) {
          showInputError(input);
          error++;
        }
      }
    }
  }
  return error;
}

async function sendForm(e) {
  e.preventDefault();

  errorMessage.textContent = '';
  const error = validationForm(form);
  const formData = new FormData(form);

  if (error === 0) {
    popupOverlay.classList.add('popup-overlay_open');
    const response = await fetch('send.php', {
      method: 'POST',
      body: formData
    })

    if (response.ok) {
      const result = await response.json();
      pressClosePopupFeedback();
      alert(result.message);
      popupOverlay.classList.remove('popup-overlay_open');
    } else {
      errorMessage.textContent = 'Ошибка';
      popupOverlay.classList.remove('popup-overlay_open');
    }
  } else {
    errorMessage.textContent = 'Заполните обязательные поля';
  }
}

buttonBurger.addEventListener('click', pressBurger);
buttonOpenForm.addEventListener('click', pressStartTravel);
buttonExit.addEventListener('click', pressClosePopupFeedback);
form.addEventListener('submit', sendForm);
