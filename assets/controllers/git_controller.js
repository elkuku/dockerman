import {Controller} from '@hotwired/stimulus';

import '../styles/git.css'

/* stimulusFetch: 'lazy' */
export default class extends Controller {

    static values = {
        dir: String,
        url: String,
    }

    static targets = [
        'status', 'repoStatus',
        'cntModified', 'cntDeleted', 'cntRenamed', 'cntUnversioned',
        'txtModified', 'txtDeleted', 'txtRenamed', 'txtUnversioned',
    ]

    isGitDir = false

    async connect() {
        this.statusTarget.innerText = '...';
        const response = await fetch(this.urlValue)
        const data = await response.json();
        if (false === data.isGitDir) {
            this.element.classList.add('noGit');
        } else {
            this.isGitDir = true
            this.element.classList.add('gitRepo');
            if (data.status.modified.length) {
                this.cntModifiedTarget.innerText = data.status.modified.length
                this.txtModifiedTarget.innerHTML = data.status.modified.join('<br>')
            }
            if (data.status.deleted.length) {
                this.cntDeletedTarget.innerText = data.status.deleted.length
                this.txtDeletedTarget.innerHTML = data.status.deleted.join('<br>')
            }
            if (data.status.renamed.length) {
                this.cntRenamedTarget.innerText = data.status.renamed.length
                this.txtRenamedTarget.innerHTML = data.status.renamed.join('<br>')
            }
            if (data.status.unversioned.length) {
                this.cntUnversionedTarget.innerText = data.status.unversioned.length
                this.txtUnversionedTarget.innerHTML = data.status.unversioned.join('<br>')
            }
        }
        this.statusTarget.innerText = '';

    }

    showInfo() {
        if (false === this.isGitDir) {
            return
        }
        if ('block' === this.repoStatusTarget.style.display) {
            this.repoStatusTarget.style.display = 'none'

        } else {
            this.repoStatusTarget.style.display = 'block'

        }
        console.log(this.repoStatusTarget.style.display);
    }
}
