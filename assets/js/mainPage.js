document.querySelector('.add-task').addEventListener('click', function() {
    let taskList = document.querySelector('.task-list');
    let newTask = document.createElement('div');
    newTask.className = 'task';
    newTask.innerHTML = `
        <input type="checkbox" id="new-task">
        <label for="new-task">Neue Aufgabe</label>
        <span class="time">Zeit</span>
    `;
    taskList.appendChild(newTask);
});
