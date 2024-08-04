import { defineStore } from "pinia";

export const useMessageStore = defineStore("message", {
	state: () => ({
		message: "",
		messageType: "error" as const,
		myTimeout: 0,
	}),

	actions: {
		error(message: any, timeOut = 5000) {
			if (typeof message === "string") {
				this.message = message;
			} else if (typeof message.response !== "undefined") {
				this.message = `Sorry, there is an error (${
					message.response.status ?? "?"
				}). ${message.response.data.message ?? ""}`;
			} else {
				this.message = message.message ?? "Sorry, there is an error.";
			}
			this.messageType = "error" as const;
			this.setTimeout(timeOut);
		},
		success(message: string, timeOut = 5000) {
			this.message = message;
			this.messageType = "success" as const;
			this.setTimeout(timeOut);
		},
		setTimeout(timeOut = 5000) {
			clearTimeout(this.myTimeout);
			if (timeOut) {
				this.myTimeout = window.setTimeout(() => this.resetMessage(), timeOut);
			}
		},
		resetMessage() {
			this.message = "";
			this.messageType = "error" as const;
			clearTimeout(this.myTimeout);
		},
	},
});
