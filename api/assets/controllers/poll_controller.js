import { Controller } from '@hotwired/stimulus';

const makeOption = (optionId, optionName, voteId, votePower) =>
  `                    <div class="relative flex items-start">\n` +
  `                        <div class="flex h-6 items-center">\n` +
  `                            <input id="${optionId}" name="${optionId}" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" data-action="poll#voted" data-poll-target="option" data-poll-vote-id="${voteId}">\n` +
  `                        </div>\n` +
  `                        <div class="ml-3 text-sm leading-6">\n` +
  `                            <label for="${optionId}" class="font-medium text-gray-900">${optionName} (${votePower} Votes)</label>\n` +
  `                        </div>\n` +
  `                    </div>`

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {
  static targets = [ "optionContainer", "newOptionInput", "option", "addOptionContainer" ]
  static values = { pollId: String, maxOptionAdd: Number, myOptionCount: Number, votePower: Number }


  initialize() {
    super.initialize();
  }

  handleError(errorMessage) {
    alert(errorMessage.replace('pollId: ', ''))
  }

  voted(event) {
    event.preventDefault();
    const isAdd = event.target.dataset.pollVoteId === undefined;
    if (isAdd) {
      fetch('/ajax/poll/option/vote', {
        method: 'POST',
        headers: {
          "Content-Type": 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          'pollId': this.pollIdValue,
          'optionId': event.target.id
        })
      })
        .then((data) => data.json())
        .then((response) => {
          if (response.status > 299) {
            this.handleError(response.detail)
            return;
          }
          event.target.checked = true
          event.target.dataset.pollVoteId = response.voteId
          const labelElement = event.target.labels[0];
          const newVoteCount = parseInt(labelElement.dataset.voteCounter ?? 0) + this.votePowerValue
          labelElement.dataset.voteCounter = `${newVoteCount}`
          labelElement.innerHTML = labelElement.innerHTML.replace(/\(.*\)/, `(${newVoteCount} Votes)`)
        })
        .catch((error) => {
          console.log(error)
        })
    } else {
      fetch('/ajax/poll/option/vote', {
        method: 'DELETE',
        headers: {
          "Content-Type": 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          'voteId': event.target.dataset.pollVoteId,
        })
      })
        .then((data) => data.json())
        .then((response) => {
          if (response.status > 299) {
            this.handleError(response.detail)
            return;
          }
          event.target.checked = false
          delete event.target.dataset.pollVoteId
          const labelElement = event.target.labels[0];
          const newVoteCount = parseInt(labelElement.dataset.voteCounter ?? 0) - this.votePowerValue
          labelElement.dataset.voteCounter = `${newVoteCount}`
          labelElement.innerHTML = labelElement.innerHTML.replace(/\(.*\)/, `(${newVoteCount} Votes)`)
        })
        .catch((error) => {
          console.log({error})
        })

    }
  }

  optionTargetConnected() {
    const options = this.optionTargets;
    if (Array.isArray(options)) {
      options.forEach((opt) => opt.checked = opt.dataset.pollVoteId !== undefined)
    }
  }

  addOption() {
    const newOptionValue = this.newOptionInputTarget.value;
    if (undefined === newOptionValue || newOptionValue === '') {
      alert('Please enter a poll option!')
      return
    }

    fetch('/ajax/poll/option', {
      method: 'POST',
      headers: {
        "Content-Type": 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        'pollId': this.pollIdValue,
        'optionName': newOptionValue
      })
    })
      .then((data) => data.json())
      .then((response) => {
        if (response.status > 299) {
          this.handleError(response.detail)
          return;
        }
        if (this.optionContainerTarget.innerHTML === 'The are no options yet.') {
          this.optionContainerTarget.innerHTML = makeOption(response.optionId, newOptionValue, response.voteId, this.votePowerValue)
        } else {
          this.optionContainerTarget.innerHTML += makeOption(response.optionId, newOptionValue, response.voteId, this.votePowerValue)
        }
        this.newOptionInputTarget.value = ''

        if (this.maxOptionAddValue >= this.myOptionCountValue +1 && this.maxOptionAddValue !== 0) {
          this.addOptionContainerTarget.style.display = 'none'
        }
      })
      .catch((error) => {
        console.log({error})
      })
  }

  connect() {
  }
}
