import {Controller} from '@hotwired/stimulus';
import {Modal} from 'bootstrap';

export default class extends Controller {
    async showModal({params: {action, id}}) {
        let r = await fetch('/containers/' + action + '/' + id)
        let response = await r.text()

        document.getElementById('containersModalLabel').textContent = action + ' ' + id
        document.getElementById('containersModalContent').textContent = response

        new Modal('#containersModal').show()
    }

    async startStop(e) {
        const element = e.currentTarget
        console.log(element)
        const icon = element.children[0]
        if (element.classList.contains('btn-danger')) {
            const r = await fetch('/containers/stop/' + e.params.id);

            const response = await r.json();
            if (response.error) {
                console.error(response.error);
                document.getElementById('containersModalLabel').textContent = 'ERROR'
                document.getElementById('containersModalContent').textContent = response.error

                new Modal('#containersModal').show()
            } else {
                element.classList.remove('btn-danger');
                element.classList.add('btn-success');
                element.title = 'Start'

                icon.classList.remove('bi-square-fill')
                icon.classList.add('bi-caret-right-fill')
            }
        } else {
            const r = await fetch('/containers/start/' + e.params.id);

            const response = await r.json();
            if (response.error) {
                console.error(response.error);
                document.getElementById('containersModalLabel').textContent = 'ERROR'
                document.getElementById('containersModalContent').textContent = response.error

                new Modal('#containersModal').show()
            } else {
                element.classList.remove('btn-success')
                element.classList.add('btn-danger')
                element.title = 'Stop'

                icon.classList.remove('bi-caret-right-fill')
                icon.classList.add('bi-square-fill')
            }
        }
    }

    async remove(e) {
        const element = e.currentTarget;
        const cardElement = element.parentElement.parentElement.parentElement.parentElement;
        const r = await fetch('/containers/remove/' + e.params.id);

        const response = await r.json();
        if (response.error) {
            console.error(response.error);
            document.getElementById('containersModalLabel').textContent = 'ERROR'
            document.getElementById('containersModalContent').textContent = response.error

            new Modal('#containersModal').show()
        } else {
            cardElement.remove();
        }
    }
}
