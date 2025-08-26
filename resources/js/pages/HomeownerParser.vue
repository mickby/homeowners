<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header">
            <h1 class="card-title">Homeowner Names Parser</h1>
            <p class="card-text text-muted">
              Upload a CSV file containing homeowner data to parse names into individual person records.
            </p>
          </div>
          <div class="card-body">
            <form @submit.prevent="handleSubmit" class="mb-4">
              <div class="mb-3">
                <label for="csvFile" class="form-label">Select CSV File</label>
                <input 
                  type="file" 
                  class="form-control" 
                  id="csvFile" 
                  accept=".csv" 
                  @change="handleFileChange"
                  required
                >
                <div class="form-text">
                  Please select a CSV file containing homeowner names to parse.
                </div>
              </div>
              <button 
                type="submit" 
                class="btn btn-primary" 
                :disabled="!selectedFile || processing"
              >
                <span v-if="processing" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                {{ processing ? 'Processing...' : 'Parse Names' }}
              </button>
            </form>

            <!-- Error Alert -->
            <div v-if="error" class="alert alert-danger" role="alert">
              <strong>Error:</strong> {{ error }}
            </div>

            <!-- Results -->
            <div v-if="results && results.length > 0" class="mt-4">
              <h3>Parsed Results</h3>
              <div class="alert alert-success" role="alert">
                Successfully parsed {{ results.length }} person record(s).
              </div>
              
              <div class="table-responsive">
                <table class="table table-striped table-hover">
                  <thead class="table-dark">
                    <tr>
                      <th>Title</th>
                      <th>First Name</th>
                      <th>Initial</th>
                      <th>Last Name</th>
                      <th>Original Input</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(person, index) in results" :key="index">
                      <td>{{ person.title || '-' }}</td>
                      <td>{{ person.first_name || '-' }}</td>
                      <td>{{ person.initial || '-' }}</td>
                      <td>{{ person.last_name || '-' }}</td>
                      <td class="text-muted">{{ person.original_input }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const selectedFile = ref(null)
const processing = ref(false)
const error = ref(null)
const results = ref([])

function handleFileChange(event) {
  selectedFile.value = event.target.files[0]
  error.value = null
  results.value = []
}

function handleSubmit() {
  if (!selectedFile.value) return
  
  const formData = new FormData()
  formData.append('csv_file', selectedFile.value)
  
  processing.value = true
  error.value = null
  
  router.post('/parse-homeowners', formData, {
    onSuccess: (page) => {
      results.value = page.props.results || []
      processing.value = false
    },
    onError: (errors) => {
      error.value = errors.csv_file || errors.message || 'An error occurred while processing the file.'
      processing.value = false
    },
    onFinish: () => {
      processing.value = false
    }
  })
}
</script>