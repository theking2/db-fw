"use strict"

/**
 * 
 * @param string selector selector (tag,#id,.class) to search
 * @param Element base 
 * @returns 
 */
const $ = (selector, base = document) => {
    let elements = base.querySelectorAll(selector);
    return (elements.length == 1) ? elements[0] : elements;
}