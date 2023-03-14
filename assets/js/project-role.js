import { api_url } from "./config"
import { $ } from "./main"
import { autocomplete } from "./autocomplete"

autocomplete($("#project"), $("#project-id"), api_url + '/project?Name=', 'Name');
autocomplete($("#student"), $("#student-id"), api_url + '/student?Fullname=', 'Fullname');


const project = document.getElementById('Project');
const id = document.getElementById('id');
const message = document.getElementById('message'); "";

const response = await fetch('/projectrole');
const data = await response.json();
const role = document.getElementById('role-id');
data.forEach(element => {
  const option = document.createElement('option');
  option.value = element.ID;
  option.innerHTML = element.Name;
  role.appendChild(option);
});

document.getElementById('add').addEventListener('click', async ev => {
  ev.preventDefault();
  const projectID = document.getElementById('project-id').value;
  const studentID = document.getElementById('student-id').value;
  const roleID = document.getElementById('role-id').value;
  const date = document.getElementById('date').value;

  let id = null;
  let url = `/studentroleproject?ProjectID=${projectID}&StudentID=${studentID}&ProjectRoleID=${roleID}&Start=${date}`;
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
    ProjectRoleID: roleID,
    Start: date,
  });
  let request;
  if (id) {
    url = `/studentroleproject/${id}`;
    request = {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: body
    };
  } else {
    url = '/studentroleproject';
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


})
