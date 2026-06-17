import React from 'react'
import { createRoot } from 'react-dom/client'
import DlcFilter from './components/dlc-filter/DlcFilter'
import type { FilterData, DlcPost } from './components/dlc-filter/types'

// Global type declaration for window.dlcFilterData
declare global {
  interface Window {
    dlcFilterData?: {
      filterData: FilterData
      initialPosts: DlcPost[]
      apiEndpoint: string
    }
  }
}

// Initialize DLC Filter when DOM is ready
const initDlcFilter = () => {
  const container = document.getElementById('dlc-filter-root')

  if (!container) {
    console.warn('DLC Filter container not found')
    return
  }

  if (!window.dlcFilterData) {
    console.warn('DLC Filter data not available')
    return
  }

  const { filterData, initialPosts, apiEndpoint } = window.dlcFilterData

  const root = createRoot(container)
  root.render(
    <React.StrictMode>
      <DlcFilter
        filterData={filterData}
        initialPosts={initialPosts}
        apiEndpoint={apiEndpoint}
      />
    </React.StrictMode>
  )
}

// Initialize on DOMContentLoaded
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initDlcFilter)
} else {
  initDlcFilter()
}
