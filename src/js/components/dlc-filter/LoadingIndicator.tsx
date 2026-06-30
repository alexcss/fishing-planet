import React from 'react'

interface LoadingIndicatorProps {
  className?: string
}

const LoadingIndicator: React.FC<LoadingIndicatorProps> = ({ className }) => {
  return (
    <div className={`absolute inset-x-0 bottom-full m-0 flex justify-center pb-10 lg:pb-15 xl:pb-25 ${className || ''}`}>
      <div className="border-accent size-32 animate-spin rounded-full border-4 border-t-transparent xl:size-48" />
    </div>
  )
}

export default LoadingIndicator
