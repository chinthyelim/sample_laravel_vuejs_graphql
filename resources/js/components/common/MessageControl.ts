import { Ref, ref } from "vue";

interface Message {
	show: boolean;
	type: "success" | "error";
	msg: string;
	timeOut: number;
}

const m: Ref<Message> = ref({
	show: false,
	type: "error" as const,
	msg: "",
	timeOut: 0,
});

export function useMessageControl() {
	const setTimeout = (timeOut = 5000) => {
		clearTimeout(m.value.timeOut);
		if (timeOut) {
			m.value.timeOut = window.setTimeout(
				() => (m.value.show = false),
				timeOut
			);
		}
	};

	const resetMessage = () => {
		m.value.msg = "";
		clearTimeout(m.value.timeOut);
		return m;
	};

	const successMsg = (msg: string) => {
		alert("oooo");
		resetMessage();
		m.value.type = "success" as const;
		m.value.msg = msg;
		setTimeout();
		return m;
	};

	const errorMsg = (msg: any) => {
		resetMessage();
		m.value.type = "error" as const;
		if (typeof msg === "string") {
			m.value.msg = msg;
		} else if (typeof msg.response !== "undefined") {
			m.value.msg = `Sorry, there is an error (${
				msg.response.status ?? "?"
			}). ${msg.response.data.message ?? ""}`;
		} else {
			m.value.msg = msg.message ?? "Sorry, there is an error.";
		}
		setTimeout();
		return m;
	};

	return { resetMessage, successMsg, errorMsg };
}
