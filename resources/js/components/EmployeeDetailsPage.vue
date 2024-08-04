<template>
	<common-page
		title="Employee Details"
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
				v-model="models.first_name"
				:readonly="!formControl.enabled"
				:bg-color="formControl.bgColor"
				:rules="rules.first_name"
				placeholder="eg. John"
				label="First Name*"
			></v-text-field>
			<v-text-field
				v-model="models.last_name"
				:readonly="!formControl.enabled"
				:bg-color="formControl.bgColor"
				:rules="rules.last_name"
				placeholder="eg. Citizen"
				label="Last Name*"
			></v-text-field>
			<v-row class="mb-6" no-gutters>
				<v-col md="8">
					<v-select
						v-model="models.company_id"
						:items="companies"
						item-title="name"
						item-value="id"
						:readonly="!formControl.enabled"
						:bg-color="formControl.bgColor"
						:rules="rules.company_id"
						label="Company*"
						density="comfortable"
					></v-select>
				</v-col>
				<v-col md="4">
					<v-layout align-end>
						<v-img
							ref="logoImgRef"
							:src="siteUrl + 'storage/' + companyLogo.logo"
							width="100"
							height="160"
						/>
					</v-layout>
				</v-col>
			</v-row>
			<v-text-field
				v-model="models.email"
				:readonly="!formControl.enabled"
				:bg-color="formControl.bgColor"
				:rules="rules.email"
				placeholder="eg. john@mysite.com"
				label="Email"
			></v-text-field>
			<v-text-field
				v-model="models.phone"
				:readonly="!formControl.enabled"
				:bg-color="formControl.bgColor"
				:rules="rules.phone.concat(rules.phoneNumber)"
				placeholder="eg. +1 (270) 705-0515"
				label="Phone"
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
</template>

<script lang="ts">
export default { name: "EmployeeDetailsPage" };
</script>

<script setup lang="ts">
import { Ref, ref, onMounted, watch } from "vue";
import { adminApi } from "../axios";
import type { Company, Employee } from "../api-client/api";
import CommonPage from "./common/CommonPage.vue";
import { useFormControl } from "./common/FormControl";
import { useMessageStore } from "../stores/MessageStore";
import {
	requiredId,
	maxLength,
	email as v_email,
	requiredAndMaxlength,
	phoneNumber as v_phoneNumber,
} from "../utils/validate_patterns";

const props = defineProps<{
	models: Employee;
	mode: string;
	siteUrl: string;
}>();

const emit = defineEmits<{
	(e: "created", value: Employee): void;
	(e: "updated", value: Employee): void;
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
	breadcrumbs.value = ["Employees", "New Employee"];
} else {
	breadcrumbs.value = ["Employees", "Edit Employee"];
}
const companies: Ref<Company[]> = ref([] as Company[]);
const companyLogo: Ref<Company> = ref({ logo: "imgNoImage.png" } as Company);

const create = async () => {
	loading.value = true;
	try {
		const response = await adminApi.createEmployee({
			EmployeeCreate: models.value,
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
		const response = await adminApi.updateEmployee({
			id,
			EmployeeUpdate: params,
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

onMounted(async () => {
	try {
		companies.value =
			(await adminApi.getCompanies()).data.data || ([] as Company[]);
		companies.value.unshift({
			id: 0,
			name: " -- Select -- ",
			email: "",
			logo: "imgNoImage.png",
			website: "",
		});
		companyLogo.value =
			companies.value.find(
				(item: Company) => item.id == props.models.company_id
			) || ({ logo: "imgNoImage.png" } as Company);
	} catch (error: any) {
		messageStore.error(error);
	}
});

const rules = {
	first_name: [requiredAndMaxlength(255)],
	last_name: [requiredAndMaxlength(255)],
	company_id: [requiredId("company is required.")],
	email: [v_email()],
	phone: [maxLength(255)],
	phoneNumber: [v_phoneNumber()],
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

watch(
	models,
	() => {
		companyLogo.value =
			companies.value.find(
				(item: Company) => item.id == props.models.company_id
			) || ({ logo: "imgNoImage.png" } as Company);
	},
	{ deep: true }
);
</script>
