<template>
	<common-page
		title="Company Details"
		:breadcrumbs="breadcrumbs"
		@cancel="cancel"
	>
		<template #title-buttons>
			<v-btn
				v-show="mode != 'create' && disabledControl"
				@click="disabledControl = !disabledControl"
				outlined
				dark
				color="teal lighten-4"
				>Edit</v-btn
			>
			<v-btn
				v-show="mode != 'create' && !disabledControl"
				@click="disabledControl = !disabledControl"
				outlined
				dark
				color="indigo-darken-3"
				>Cancel Edit</v-btn
			>
		</template>
		<v-form @submit.prevent="confirm(mode === 'create')">
			<v-text-field
				v-model="models.name"
				:readonly="!formControl.enabled"
				:bg-color="formControl.bgColor"
				:rules="rules.name"
				placeholder="eg. MySite Ltd"
				label="Name*"
			></v-text-field>
			<v-text-field
				v-model="models.email"
				:readonly="!formControl.enabled"
				:bg-color="formControl.bgColor"
				:rules="rules.email"
				placeholder="eg. mysite@mysite.com"
				label="Email"
			></v-text-field>
			<!--<v-img ref="logoImgRef" :src="models.logo" width="100" height="160" />-->
			<img
				id="logoImgRef"
				ref="logoImgRef"
				:src="siteUrl + 'storage/' + models.logo"
				width="100"
				class="mr-5"
			/>
			<v-btn
				@click="fileLogo?.click()"
				:disabled="!formControl.enabled"
				outlined
				dark
				color="indigo-darken-3"
				>Upload Logo</v-btn
			>
			<v-text-field
				v-model="models.logo"
				readonly
				bg-color="light-blue-lighten-5"
				:rules="rules.logo"
				label="Logo File"
				class="mt-7"
			></v-text-field>
			<v-text-field
				v-model="models.website"
				:readonly="!formControl.enabled"
				:bg-color="formControl.bgColor"
				:rules="rules.noWhiteSpace.concat(rules.website)"
				placeholder="eg. https://mysite.com"
				label="Website"
			></v-text-field>
			<v-card-actions>
				<v-spacer></v-spacer>
				<v-btn
					v-show="mode == 'create' || !disabledControl"
					type="submit"
					outlined
					dark
					color="indigo-darken-3"
					:loading="loading"
					>{{ mode == "create" ? "Save" : "Update" }}</v-btn
				>
				<v-btn @click="cancel" outlined dark color="indigo-darken-3">{{
					disabledControl ? (mode == "create" ? "Cancel" : "Close") : "Cancel"
				}}</v-btn>
			</v-card-actions>
		</v-form>
	</common-page>
	<common-dialog
		v-model="dialogMessage.show"
		:message="dialogMessage.message"
		:type="dialogMessage.type"
		@ok="ok"
	></common-dialog>
	<input
		ref="fileLogo"
		type="file"
		accept=".jpg,.jpeg,.png"
		style="display: none"
		@change="uploadLogo('jpg,jpeg,png')"
	/>
</template>

<script lang="ts">
export default { name: "CompanyDetailsPage" };
</script>

<script setup lang="ts">
import { Ref, ref, watch } from "vue";
import { adminApi } from "../axios";
import type { Company } from "../api-client/api";
import CommonPage from "./common/CommonPage.vue";
import CommonDialog, { DialogCtl } from "./common/CommonDialog.vue";
import { useFormControl } from "./common/FormControl";
import { useMessageStore } from "../stores/MessageStore";
import {
	requiredAndMaxlength,
	maxLength,
	email as v_email,
	noWhiteSpace as v_noWhiteSpace,
} from "../utils/validate_patterns";
//import { VImg } from "vuetify/components";

const props = defineProps<{
	models: Company;
	mode: string;
	siteUrl: string;
}>();

const emit = defineEmits<{
	(e: "created", value: Company): void;
	(e: "updated", value: Company): void;
	(e: "cancel"): void;
}>();

const messageStore = useMessageStore();
const models = ref(props.models);
const loading = ref(false);
const disabledControl = ref(true);
const { enabled, bgColor } = useFormControl({
	mode: props.mode == "create" ? ("create" as const) : ("update" as const),
	enabledEdit: !disabledControl.value || props.mode == "create",
});
const formControl = ref({
	enabled: enabled.value,
	bgColor: bgColor.value,
});
const breadcrumbs = ref([] as Array<any>);
if (props.mode == "create") {
	breadcrumbs.value = ["Compamnies", "New Company"];
} else {
	breadcrumbs.value = ["Compamnies", "Edit Company"];
}
//const logoImgRef = ref<InstanceType<typeof VImg>>();
const logoImgRef = ref<InstanceType<typeof HTMLImageElement>>();
const fileLogo = ref<InstanceType<typeof HTMLInputElement>>();
const dialogMessage: Ref<DialogCtl> = ref({
	show: false,
	message: "",
	type: "OKOnly" as const,
});

const validFileExtension = (fileExt: any) => {
	if (fileLogo.value?.value.indexOf(".") == -1) {
		return false;
	}
	let validExtensions: any = [];
	const ext =
		fileLogo.value?.value
			.substring(fileLogo.value?.value.lastIndexOf(".") + 1)
			.toLowerCase() || "";
	validExtensions = fileExt.split(",");
	for (let i = 0; i < validExtensions.length; i++) {
		if (ext == validExtensions[i]) {
			return true;
		}
	}
	dialogMessage.value.message =
		"The file extension " + ext.toUpperCase() + " is not allowed!";
	dialogMessage.value.show = true;
	return false;
};

const validFileSize = () => {
	if (!fileLogo.value?.files) {
		return false;
	}
	if (
		fileLogo.value?.files[0] !== undefined &&
		fileLogo.value?.files[0].size / 1024 / 1024 > 3
	) {
		dialogMessage.value.message = "Logo file cannot exceed 3mb!";
		dialogMessage.value.show = true;
		return false;
	}
	return true;
};

const getBase64Image = () => {
	if (!logoImgRef.value) {
		return "";
	}
	const canvas = document.createElement("canvas");
	canvas.width = logoImgRef.value.width;
	canvas.height = logoImgRef.value.height;
	const ctx = canvas.getContext("2d");
	if (!ctx) {
		return "";
	}
	ctx.drawImage(
		logoImgRef.value,
		0,
		0,
		logoImgRef.value.width,
		logoImgRef.value.height
	);
	const imgData = ctx.getImageData(100, 100, canvas.width, canvas.height);
	for (let i = 0; i < imgData.data.length; i += 4) {
		imgData.data[i] = 255 - imgData.data[i];
		imgData.data[i + 1] = 255 - imgData.data[i + 1];
		imgData.data[i + 2] = 255 - imgData.data[i + 2];
		imgData.data[i + 3] = 255;
	}
	ctx.putImageData(imgData, 100, 100);
	return canvas.toDataURL("image/png");
};

const uploadLogo = (allowFileExts: any) => {
	if (
		!fileLogo.value?.files ||
		!validFileExtension(allowFileExts) ||
		!validFileSize()
	) {
		return;
	}
	models.value.logo = fileLogo.value?.files[0].name;
	if (FileReader && fileLogo.value?.files && fileLogo.value?.files.length) {
		const fr = new FileReader();
		fr.onload = function () {
			if (!logoImgRef.value) {
				return;
			}
			logoImgRef.value.src = fr.result as string;
		};
		fr.readAsDataURL(fileLogo.value?.files[0]);
	}
};

const create = async () => {
	loading.value = true;
	try {
		const companyCreateModels: any = models.value;
		companyCreateModels["logoContent"] = getBase64Image();
		const response = await adminApi.createCompany({
			CompanyCreate: companyCreateModels,
		});
		emit("created", response.data);
	} catch (error: any) {
		messageStore.error(error);
	} finally {
		loading.value = false;
	}
};

const update = async () => {
	loading.value = true;
	try {
		const { id, ...params } = models.value;
		const companyUpdateModels: any = params;
		companyUpdateModels["logoContent"] = getBase64Image();
		const response = await adminApi.updateCompany({
			id,
			CompanyUpdate: companyUpdateModels,
		});
		emit("updated", response.data);
	} catch (error: any) {
		messageStore.error(error);
	} finally {
		loading.value = false;
	}
};

const confirm = async (addNew: boolean) => {
	if (addNew) {
		create();
	} else {
		update();
	}
};

const cancel = () => {
	emit("cancel");
};

const ok = () => {
	dialogMessage.value.show = false;
};

const rules = {
	name: [requiredAndMaxlength(255)],
	email: [v_email()],
	logo: [maxLength(255)],
	website: [maxLength(512)],
	noWhiteSpace: [v_noWhiteSpace()],
};

watch(
	disabledControl,
	() => {
		let { enabled, bgColor } = useFormControl({
			mode: props.mode == "create" ? ("create" as const) : ("update" as const),
			enabledEdit: !disabledControl.value || props.mode == "create",
		});
		formControl.value.enabled = enabled.value;
		formControl.value.bgColor = bgColor.value;
	},
	{ deep: true }
);
</script>
