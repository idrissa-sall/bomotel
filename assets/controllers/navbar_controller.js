import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["menu", "button"];

    toggle() {
        const isOpen =
            this.buttonTarget.getAttribute("aria-expanded") === "true";

        this.menuTarget.classList.toggle("hidden", isOpen);
        this.buttonTarget.setAttribute("aria-expanded", String(!isOpen));
    }

    close() {
        if (this.buttonTarget.getAttribute("aria-expanded") !== "true") {
            return;
        }

        this.menuTarget.classList.add("hidden");
        this.buttonTarget.setAttribute("aria-expanded", "false");
        this.buttonTarget.focus();
    }
}
