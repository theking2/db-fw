"use strict";
import { debounce, $ } from "./utils.js"

export const autocomplete = (input, inputID, ajaxUrl, fieldName) => {
  let currentFocus = -1;

  input.addEventListener(
    'input',
    debounce(async (e) => {
      let a, i, val = input.value;
      closeAllLists();
      if (val.length > 0) {
        addWaiting(input);
        const result = await fetch(ajaxUrl + val + '*');
        if (result.status === 204) {
          $('.autocomplete-waiting',input.parentNode).textContent = 'nichts gefunden';
          removeWaiting(input);
          return;
        }
        if (result.status !== 200) {
          $('.autocomplete-waiting',input.parentNode).textContent = 'fehler';
          removeWaiting(input);
          return;
        }
        const data = await result.json();
        removeWaiting(input);
        const list = document.getElementById('select-list');

        const a = document.createElement('div');
        a.setAttribute('id', fieldName + 'autocomplete-list');
        a.setAttribute('class', 'autocomplete-items');
        input.parentNode.appendChild(a);

        let i = 10;
        data.every(item => {
          const b = document.createElement('div');
          b.innerHTML = item[fieldName];
          b.dataset.id = item.ID;
          b.addEventListener('click', e => {
            inputID.value = b.dataset.id;
            input.value = b.innerHTML;
            closeAllLists();

            inputID.dispatchEvent(new Event('change'));
          });
          a.appendChild(b);
          return (i--);
        });
      }
    }, 500)
  );

  function addWaiting(input) {
    let wait = document.createElement('div')
    wait.classList.add('autocomplete-waiting');
    wait.textContent = "loading";
    input.parentNode.appendChild(wait);
  }
  function removeWaiting(input) {
    setTimeout(_ => {
      input.parentNode.removeChild(document.getElementsByClassName('autocomplete-waiting')[0]);
    }, 1000);
  }

  input.addEventListener("keydown", debounce((e) => {
    var x = document.getElementById(fieldName + "autocomplete-list");
    if (x) x = x.getElementsByTagName("div");
    switch (e.keyCode) {
      case 40: // down
        currentFocus++;
        addActive(x);
        break;
      case 38: // up
        currentFocus--;
        addActive(x);
        break;
      case 13: // enter
        e.preventDefault();
        if (currentFocus > -1) {
          if (x) x[currentFocus].click();
        }
        break;
      default:
        break;
    }
  }, 1));


  /**
   * 
   * @param {HTMLElement[]} x
   */
  function addActive(x) {
    if (!x) return false;
    removeActive(x);
    if (currentFocus >= x.length) currentFocus = 0;
    if (currentFocus < 0) currentFocus = (x.length - 1);
    x[currentFocus].classList.add("autocomplete-active");

    inputID.dispatchEvent(new Event('change'));
  }
  /**
   * Remove active class from all autocomplete items
   */
  function removeActive(x) {
    [].forEach.call(x, item => {
      item.classList.remove("autocomplete-active");
    });
  }
  /**
   * Close all autocomplete lists in the document, except the one passed as an argument.
   * 
   * @param {HTMLElement} node - The node to ignore.
   */
  function closeAllLists(elmnt) {
    const x = document.querySelectorAll(".autocomplete-items");
    x.forEach(item => {
      if (elmnt != item && elmnt != input) {
        item.parentNode.removeChild(item);
      }
    });
  }
  /*execute a function when someone clicks in the document:*/
  document.addEventListener("click", function (e) {
    closeAllLists(e.target);
  });

};