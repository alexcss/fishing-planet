import React from 'react'

const LoadingIndicator: React.FC = () => {
  return (
    <div className="flex justify-center py-80">
      <div className="w-48 h-48 border-4 border-accent border-t-transparent rounded-full animate-spin" />
    </div>
  )
}

export default LoadingIndicator
