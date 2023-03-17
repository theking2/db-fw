import { api_url } from "./config.js"
import { $ } from "./utils.js"
import { autocomplete } from "./autocomplete.js"


document.addEventListener('DOMContentLoaded', e => {
  $('#equipment-id').addEventListener('change', e => {
    updateReservation($('#equipment-id').value);
  });

  const message = $('#message'); "";

  $('#add').addEventListener('click', async ev => {
    ev.preventDefault();

    const reservation = {
      EquipmentID: $('#equipment-id').value,
      StudentID: $('#student-id').value,
      Start: $('#start').value,
      End: $('#end').value,
    };

    let id = null;
    let url = api_url + `/equipment_reservation?EquipmentID=${reservation.EquipmentID}&StudentID=${reservation.StudentID}&Start=${reservation.Start}`;
    let response = await fetch(url);
    if (!response.ok) {
      message.innerHTML = `Fehler: ${response.status}`;
    } else if (response.status === 200) {
      result = await response.json();
      id = result[0].ID;
    }

    const body = JSON.stringify(reservation);

    let request;
    if (id) {
      url = api_url + `/equipment_reservation/${id}`;
      request = { method: 'PUT', headers: { 'Content-Type': 'application/json' }, body: body };
    } else {
      url = api_url + '/equipment_reservation';
      request = {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: body
      };
    }
    response = await fetch(url, request);
    if (!response.ok) {
      message.textContent = `Fehler: ${response.status}`;
    } else {
      message.textContent = `${response.status}: ${response.statusText}`;
    }

    updateReservation(reservation.EquipmentID);
  })
});
async function updateReservation(id) {
  const table = $('#reservations tbody');
  table.innerHTML = '';
  const response = await fetch(api_url + `/reservationview?EquipmentID=${id}`);

  if (response.ok) {
    const data = await response.json();
    table.innerHTML = '';
    data.forEach(row => {
      const tableRow = table.insertRow();
      tableRow.dataset.Id = row.ID;
      tableRow.insertCell().textContent = row.Equipment;
      tableRow.insertCell().textContent = row.Number;
      tableRow.insertCell().textContent = row.Fullname;
      tableRow.insertCell().textContent = row.Start;
      tableRow.insertCell().textContent = row.End;
    });
  }
}
async function deleteReservationEntry(event) {
  const id = event.target.parentElement.dataset.Id;
  if (id === undefined) return;
  const request = {
    method: 'DELETE',
  };
  const response = await fetch(api_url + '/equipment_reservation/' + id, request);
  if (response.status === 200) {
    updateVacationList();
    message.textContent = 'Urlaub entfernt';
  }
}
$('#reservations').addEventListener('click', deleteReservationEntry, false);



autocomplete($("#equipment"), $("#equipment-id"), api_url + '/equipment?Name=', 'Name');
autocomplete($("#student"), $("#student-id"), api_url + '/student?Fullname=', 'Fullname');
