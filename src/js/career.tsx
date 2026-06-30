import React from 'react'
import { createRoot } from 'react-dom/client'
import JobsFilter from './components/career-jobs/JobsFilter'
import type { JobFilterData, JobPost } from './components/career-jobs/types'

declare global {
  interface Window {
    careerJobsData?: {
      filterData: JobFilterData
      initialJobs: JobPost[]
      initialTotal: number
      initialPage?: number
      perPage: number
      apiEndpoint: string
    }
  }
}

const initCareerJobs = () => {
  const container = document.getElementById('career-jobs-root')

  if (!container) {
    console.warn('Career jobs container not found')
    return
  }

  if (!window.careerJobsData) {
    console.warn('Career jobs data not available')
    return
  }

  const { filterData, initialJobs, initialTotal, initialPage, perPage, apiEndpoint } = window.careerJobsData

  const root = createRoot(container)
  root.render(
    <React.StrictMode>
      <JobsFilter
        filterData={filterData}
        initialJobs={initialJobs}
        initialTotal={initialTotal}
        initialPage={initialPage}
        perPage={perPage}
        apiEndpoint={apiEndpoint}
      />
    </React.StrictMode>
  )
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initCareerJobs)
} else {
  initCareerJobs()
}
