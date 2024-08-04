<template>
	<common-page
		v-show="ctrl == 'list'"
		ref="CommonPageRef"
		title="Employees"
		:breadcrumbs="['Employees']"
		@delete="confirmDelete"
	>
		<!-- title button -->
		<template #title-buttons>
			<v-tooltip location="bottom">
				<template v-slot:activator="{ props }">
					<v-btn
						v-bind="props"
						@click="refreshList('')"
						outlined
						dark
						color="primary"
						icon="refresh"
						class="mr-2"
						>R
					</v-btn>
				</template>
				<span>Refresh</span>
			</v-tooltip>
			<v-tooltip location="bottom">
				<template v-slot:activator="{ props }">
					<v-btn
						v-bind="props"
						@click="addNew"
						outlined
						dark
						color="primary"
						icon="mdi-add"
						>+
					</v-btn>
				</template>
				<span>Add New</span>
			</v-tooltip>
		</template>

		<!-- Table -->
		<v-card>
			<v-card-text>
				<EasyDataTable
					v-model:server-options="table.serverOptions"
					:server-items-length="table.serverItemsLength"
					:loading="table.loading"
					:headers="table.headers"
					:items="table.items"
					hide-footer
				>
					<template #item-company_logo="item">
						<img :src="siteUrl + 'storage/' + item.company_logo" width="50" />
					</template>
					<template #item-operation="item">
						<div class="operation-wrapper">
							<v-btn
								@click="edit(item)"
								outlined
								dark
								color="teal lighten-4"
								icon="mdi-edit"
								class="mr-2"
								>E<!--<v-icon>edit</v-icon>-->
							</v-btn>
							<v-btn
								@click="promptConfirmDelete(item)"
								outlined
								dark
								color="red lighten-4"
								icon="mdi-delete"
								>D<!--<v-icon>delete</v-icon>-->
							</v-btn>
						</div>
					</template>
				</EasyDataTable>
			</v-card-text>
			<v-card-actions>
				<div class="ml-3">
					Page {{ table.serverOptions.page + " of " + table.serverItemsLength }}
				</div>
				<v-spacer></v-spacer>
				<v-btn
					@click="prevPage"
					outlined
					dark
					color="indigo-darken-3"
					:disabled="table.serverOptions.page <= 1"
					>Previous Page</v-btn
				>
				<v-btn
					@click="nextPage"
					outlined
					dark
					color="indigo-darken-3"
					:disabled="table.serverOptions.page > table.serverItemsLength - 1"
					>Next Page</v-btn
				>
			</v-card-actions>
		</v-card>
	</common-page>
	<employee-details-page
		v-if="ctrl == 'details'"
		:mode="mode"
		:models="models"
		:siteUrl="siteUrl"
		@created="refreshList('New employee has been successfully added!')"
		@updated="updateSelectedRow"
		@cancel="ctrl = 'list'"
	></employee-details-page>
</template>

<script lang="ts">
export default { name: "EmployeesPage" };
</script>

<script setup lang="ts">
import { Ref, ref } from "vue";
import { adminApi } from "../axios";
import type { Employee } from "../api-client/api";
import type { Header, Item, ServerOptions } from "vue3-easy-data-table";
import CommonPage from "../components/common/CommonPage.vue";
import EmployeeDetailsPage from "../components/EmployeeDetailsPage.vue";
import { useMessageStore } from "../stores/MessageStore";

defineProps<{
	siteUrl: string;
}>();

const messageStore = useMessageStore();

const table = ref({
	headers: [
		{ text: "Company Logo", value: "company_logo", width: 75, sortable: true },
		{ text: "Company Name", value: "company_name", sortable: true },
		{ text: "First Name", value: "first_name", sortable: true },
		{ text: "Last Name", value: "last_name", sortable: true },
		{ text: "Email", value: "email", sortable: true },
		{ text: "Phone", value: "phone", sortable: true },
		{ text: "Operation", value: "operation", fixed: true, width: 75 },
	] as Header[],
	items: [] as Item[] as Employee[],
	loading: false,
	serverItemsLength: 0,
	serverOptions: {
		page: 1,
		rowsPerPage: 10,
		sortBy: "id",
		sortType: "desc",
	} as ServerOptions,
});
const CommonPageRef = ref<InstanceType<typeof CommonPage>>();
const ctrl = ref("list");
const mode = ref("");
const models: Ref<Employee> = ref({} as Employee);

const prevPage = () => {
	table.value.serverOptions.page--;
	refreshList("");
};

const nextPage = () => {
	table.value.serverOptions.page++;
	refreshList("");
};

const refreshList = async (successMsg = "") => {
	if (successMsg) {
		messageStore.success(successMsg);
	}
	ctrl.value = "list";
	table.value.loading = true;
	try {
		const returnData =
			(
				await adminApi.getEmployees({
					current_page_number: table.value.serverOptions.page,
					rows_per_page: table.value.serverOptions.rowsPerPage,
				})
			).data || ([] as Employee[]);
		table.value.items = returnData.data;
		table.value.serverItemsLength = Math.ceil(
			returnData.total_rows / table.value.serverOptions.rowsPerPage
		);
		if (table.value.serverOptions.page == 0 && table.value.serverItemsLength) {
			table.value.serverOptions.page = 1;
		} else if (!table.value.serverItemsLength) {
			table.value.serverOptions.page = 0;
		}
	} catch (error: any) {
		messageStore.error(error);
	} finally {
		table.value.loading = false;
	}
};

const addNew = () => {
	models.value = {
		id: 0,
		first_name: "",
		last_name: "",
		company_id: 0,
		email: "",
		phone: "",
		company_logo: "",
		company_name: "",
	} as Employee;
	mode.value = "create";
	ctrl.value = "details";
};

const edit = (item: any) => {
	mode.value = "update";
	ctrl.value = "details";
	const { key, ...params } = item;
	models.value = item;
};

const updateSelectedRow = (updated: Employee) => {
	messageStore.success("Selected employee has been successfully updated!");
	ctrl.value = "list";
	const index = table.value.items.findIndex((item) => item.id === updated.id);
	table.value.items.splice(index, 1, updated);
};

const promptConfirmDelete = (row: any) => {
	CommonPageRef.value?.promptConfirmDelete({
		id: row.id,
		name: row.first_name + " " + row.last_name,
	});
};

const confirmDelete = async (id: number) => {
	try {
		messageStore.message = "";
		await adminApi.deleteEmployee({ id });
		if (table.value.items.length == 1) {
			table.value.serverOptions.page = table.value.serverItemsLength - 1;
		}
		refreshList("Selected employee has been successfully deleted!");
	} catch (error: any) {
		messageStore.error(error);
	}
};

refreshList("");
</script>
