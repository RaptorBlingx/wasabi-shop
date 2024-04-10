// TODO: use local files
import { loadingController } from "https://cdn.jsdelivr.net/npm/@ionic/core/dist/ionic/index.esm.js";
import axios from 'https://cdnjs.cloudflare.com/ajax/libs/axios/1.6.8/esm/axios.js';
import Router from "./router.js";

const router = new Router()

const showLoading = async (message = undefined, duration = undefined) => {
    const loading = await loadingController.create({
        message: message,
        duration: duration
    });
    loading.present();
};

document.querySelectorAll('[data-approve]').forEach(button => {
    button.onclick = () => {
        const developerId = button.dataset.approve
        const alert = document.createElement('ion-alert')
        alert.message = 'Are you sure to approve this application?'
        alert.buttons = [
            {
                text: 'Cancel',
                role: 'cancel',
            },
            {
                text: 'Yes!',
                role: 'confirm',
                handler: () => {
                  showLoading()
                  approveDeveloper(developerId)
                },
            }
        ]
        presentAlert(alert)
    }
})

document.querySelectorAll('[data-block]').forEach(button => {
    button.onclick = () => {
        const developerId = button.dataset.block
        const alert = document.createElement('ion-alert')
        alert.message = 'Are you sure to block this application?'
        alert.buttons = [
            {
                text: 'Cancel',
                role: 'cancel',
            },
            {
                text: 'Yes!',
                role: 'confirm',
                handler: () => {
                  showLoading()
                  blockDeveloper(developerId)
                },
            }
        ]
        presentAlert(alert)
    }
})

async function presentAlert(alert) {
    document.body.appendChild(alert);
    await alert.present();
}

function blockDeveloper(developerId) {
    const path = router.generate('developers_block', {id: developerId})
    axios.post(path).then(r => {
        const span = document.querySelector(`[data-status="${developerId}"]`)
        if (span) {
            span.classList.remove('status-pending')
            span.classList.remove('status-approved')
            span.classList.add('status-refused')
            span.innerHTML = 'refused'
        }
        location.reload()
    })
}

function approveDeveloper(developerId) {
    const path = router.generate('developers_approve', {id: developerId})
    axios.post(path).then(r => {
        const span = document.querySelector(`[data-status="${developerId}"]`)
        if (span) {
            span.classList.remove('status-pending')
            span.classList.remove('status-refused')
            span.classList.add('status-approved')
            span.innerHTML = 'approved'
        }
        location.reload()
    })
}