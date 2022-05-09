"use strict";

const autocomplete = (input, inputID, ajaxUrl, fieldName) => {
  let currentFocus = -1;

  input.addEventListener(
    'input',
    debounce( async(e) => {
      let a, i, val = input.value;
      closeAllLists();
      if (val.length > 0) {
        const result = await fetch(ajaxUrl + val + '*');
        const data = await result.json();
        const list = document.getElementById('select-list');

        const a = document.createElement('div');
        a.setAttribute('id', this.id + 'autocomplete-list');
        a.setAttribute('class', 'autocomplete-items');
        input.parentNode.appendChild(a);

        let i = 10;
        data.every(project => {
          const b = document.createElement('div');
          b.innerHTML = project[fieldName];
          b.dataset.id = project.ID;
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

  function throttle(func, wait) {
    let lastTime = 0;

    return (...args) => {
      const now = Date.now();

      let delay = now - lastTime;
      console.log(delay);
      if (delay >= wait) {
        func(...args);

        lastTime = now;
      }
    };
  };

  function debounce (func, wait) {
    let timeout;

    return (...args) => {
      if (timeout) clearTimeout(timeout);

      timeout = setTimeout(() => func(...args), wait);
    };
  };

  input.addEventListener("keydown", (e) => {
    var x = document.getElementById(this.id + "autocomplete-list");
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
  });


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
    const x = document.getElementsByClassName("autocomplete-items");
    [].forEach.call(x, item => {
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