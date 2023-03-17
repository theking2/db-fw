import { api_url } from "./config.js"
import { $ } from "./utils.js"
import { autocomplete } from "./autocomplete.js"

const project = $('#Project');
const id = $('#id');
let message = $('#message'); "";

$('#add').addEventListener('click', async ev => {
  ev.preventDefault();
  const projectID = $('#project-id').value;
  const studentID = $('#student-id').value;
  const date = $('#date').value;
  const minutes = $('#minutes').value;

  let id = null;
  let url = api_url + `/timesheet?ProjectID=${projectID}&StudentID=${studentID}&Date=${date}`;
  let response = await fetch(url);
  if (!response.ok) {
    message.innerHTML = `Fehler: ${response.status}`;
  } else if (response.status === 200) {
    result = await response.json();
    id = result[0].ID;
  }

  const body = JSON.stringify({
    ProjectID: projectID,
    StudentID: studentID,
    Date: date,
    Minutes: minutes
  });
  let request;
  if (id) {
    url = api_url + `/timesheet/${id}`;
    request = {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: body
    };
  } else {
    url = api_url + '/timesheet';
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

  updateTimeSheet(studentID);
})


async function updateTimeSheet(id) {
  const timeSheet = document.querySelector('#time-sheet tbody');
  timeSheet.innerHTML = '';
  const response = await fetch(api_url + `/timesheetview?StudentID=${id}`);
  const data = await response.json();

  data.forEach(row => {
    const tr = document.createElement('tr');
    const tableRow = timeSheet.insertRow();
    tableRow.dataset.id = row.ProjectID;
    tableRow.insertCell().textContent = row.ProjectName;
    tableRow.insertCell().textContent = row.Date;
    tableRow.insertCell().textContent = row.Minutes;
  });
}

autocomplete($('#project'), $('#project-id'), api_url + '/project?Name=', 'Name');
autocomplete($('#student'), $('#student-id'), api_url + '/student?Fullname=', 'Fullname');

$('#student-id').addEventListener('change', e => {
  updateTimeSheet($('#student-id').value);
});