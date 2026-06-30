import React from 'react'
import JobCard from './JobCard'
import type { JobPost } from './types'

interface JobsGridProps {
  jobs: JobPost[]
}

const JobsGrid: React.FC<JobsGridProps> = ({ jobs }) => {
  return (
    <div className="grid grid-cols-1 gap-24 md:grid-cols-2 lg:grid-cols-3 xl:gap-40">
      {jobs.map((job) => (
        <JobCard key={job.id} job={job} />
      ))}
    </div>
  )
}

export default JobsGrid
