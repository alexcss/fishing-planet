import React from 'react'

const EmptyState: React.FC = () => {
  return (
    <div className="text-center py-80">
      <p className="h4 text-white/70">
        No DLCs found matching your filters.
      </p>
      <p className="fp-text-body text-white/50 mt-16">
        Try adjusting your filter criteria.
      </p>
    </div>
  )
}

export default EmptyState
