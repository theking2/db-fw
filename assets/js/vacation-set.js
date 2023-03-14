"use strict"
import { $ } from "./main"
import { api_url } from "./config"
import { autocomplete } from "./autocomplete"

const main = document.getElementsByTagName('main')[0];

const vacationType = $('#vacation-type');
const student = $('#student');

const start = $('#start');
const end = $('#end');

const message = $('#message');
const addButton = $('#add');

/**
 * Send request to the API
 * @param event ev
 */
async function sendRequest(ev) {
  ev.preventDefault();
  main.style.cursor = 'wait';

  const vacation = {
    StudentID: student.value,
    VacationTypeID: vacationType.value,
    FromDate: start.value,
    ToDate: end.value,
  };
  let request = {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(vacation)
  };
  let response = await fetch(api_url + '/vacation', request);
  if (response.ok) {
    message.textContent = 'Ferienanfrage erfolgreich eingetragen';
    updateVacationList();
  } else {
    message.textContent = 'Fehler beim Eintragen der Ferienanfrage';
  }
  main.style.cursor = 'pointer';
}
addButton.addEventListener('click', sendRequest);

/**
 * Update the list of Vacations
 */
async function updateVacationList() {
  const table = $('#vacations tbody');

  const response = await fetch(api_url + `/vacationview?StudentID=${student.value}`);
  if (response.ok) {
    switch (response.status) {
      case 200:
        const data = await response.json();

        /* clear the table */
        table.innerHTML = '';
        /* add the rows from the data to the table */
        data.forEach(row => {
          const tableRow = table.insertRow();
          tableRow.dataset.Id = row.ID;
          tableRow.insertCell().textContent = row.Fullname;
          tableRow.insertCell().textContent = row.VacationType;
          tableRow.insertCell().textContent = row.FromDate;
          tableRow.insertCell().textContent = row.ToDate;
        });
        table.parentNode.style.display = 'table';
        break;

      case 204:
        table.parentNode.style.display = 'none';
        message.textContent = 'Kein Urlaub';
        break;

      default:
        table.parentNode.style.display = 'none';
        message.textContent = 'Fehler API ' + response.status;
        break;
    }
  } else {
    table.parentNode.style.display = 'none';
    message.textContent = 'Fehler API ' + response.status;
  }
}
student.addEventListener('change', updateVacationList);

async function deleteVacationEntry(event) {
  const id = event.target.parentElement.dataset.Id;
  if (id === undefined) return;
  const request = {
    method: 'DELETE',
  };
  const response = await fetch(api_url + '/vacation/' + id, request);
  if (response.status === 200) {
    updateVacationList();
    message.textContent = 'Urlaub entfernt';
  }
}
$('#vacations').addEventListener('click', deleteVacationEntry);

/**
* create the option list for select element
*/
async function loadOptions(select_element, url, setfunction) {
  const response = await fetch(url);
  if (response.ok) {
    select_element.innerHTML = '';
    const options = await response.json();
    options.forEach(option => {
      const option_element = document.createElement("option");
      [ option_element.value, option_element.text ] = setfunction(option);
      select_element.add(option_element);
    });
  }
}

loadOptions($("#vacation-type"), api_url + '/vacationtype', o => [ o.ID, o.Name ]);
loadOptions($("#student"), api_url + '/student', o=> [o.ID, o.Fullname]);
