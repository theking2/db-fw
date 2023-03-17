"use strict"
import { api_url } from "./config"
import { $ } from "./utils"
import { autocomplete } from "./autocomplete"


$('#project-type').onchange = async ev => {
  const projectID = document.getElementById('pid').value;
  const projectTypeID = ev.target.value;
  const response = await fetch(api_url + `./project/${projectID}`, {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      TypeID: projectTypeID
    })
  });
  if (response.ok) {
    location.reload();
  }
};
$('#coach').onchange = async ev => {
  const projectID = document.getElementById('pid').value;
  const coachID = ev.target.value;
  const response = await fetch(api_url + `./project/${projectID}`, {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      Coach: coachID
    })
  });
  if (response.ok) {
    location.reload();
  }
};