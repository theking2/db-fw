"use strict";
/**
 * 
 * @param string selector selector (tag,#id,.class) to search
 * @param Element base 
 * @returns 
 */
export function $ (selector, base = document) {
    let elements = base.querySelectorAll(selector);
    return (elements.length == 1) ? elements[0] : elements;
}
/**
 * throttle wait for 
 * @param {*} func cllled
 * @param {*} wait ms to wait
 * @returns 
 */
export function throttle(func, wait) {
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

/**
 * debounce
 * @param {function} func to call
 * @param {int} wait ms to wait
 * @returns 
 */
export function debounce(func, wait) {
  let timeout;

  return (...args) => {
    if (timeout) clearTimeout(timeout);

    timeout = setTimeout(() => func(...args), wait);
  };
};
